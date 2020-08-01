use crate::{Member, MemberBuilder};
use async_std::io::prelude::*;
use async_std::io::{ErrorKind, Read, Result};
use chrono::{Date, NaiveDate, Utc};
use smallvec::SmallVec;
use std::pin::Pin;

pub async fn parse(read: Pin<Box<dyn Read>>) -> Result<Vec<Member>> {
    let mut members = Vec::new();

    let start_symbols = [0u8, 0u8, 0x4cu8, 0x80u8];

    let mut read = read;

    let mut window = SmallVec::<[u8; 64]>::new();
    loop {
        let mut buffer = [0u8; 32];
        match read.read_exact(&mut buffer).await {
            Ok(_) => {
                if window.len() as usize > buffer.len() {
                    window.drain(0..(window.len() as usize - buffer.len()));
                }

                window.extend_from_slice(&buffer[..]);

                let data = window.iter().copied().collect::<Vec<u8>>();

                if let Some(v) = data
                    .windows(start_symbols.len())
                    .position(|window| window[..] == start_symbols[..])
                {
                    let data = &data[(v + start_symbols.len())..];

                    let mut member_data_buffer = vec![0; 3200];
                    member_data_buffer[..data.len()].clone_from_slice(data);
                    read.read_exact(&mut member_data_buffer[data.len()..])
                        .await?;

                    let (s, _, _) = encoding_rs::WINDOWS_1250.decode(&member_data_buffer[..]);
                    let chars = s.chars().collect::<Vec<_>>();

                    let id = chars[0..10]
                        .iter()
                        .copied()
                        .collect::<String>()
                        .parse::<usize>()
                        .expect("The member ID should be parseble as number");
                    let member = MemberBuilder::default()
                        .id(id)
                        .salutation(to_opt_string(&chars[10..25]))
                        .title(to_opt_string(&chars[25..60]))
                        .first_name(to_opt_string(&chars[60..95]))
                        .last_name(to_opt_string(&chars[95..165]))
                        .street(to_opt_string(&chars[165..200]))
                        .zipcode(to_opt_string(&chars[200..205]))
                        .city(to_opt_string(&chars[205..245]))
                        .related_member_id(
                            to_opt_string(&chars[1432..1442])
                                .map(|id| id.parse::<usize>().ok())
                                .flatten()
                                .filter(|id| *id > 0),
                        )
                        .birth(parse_date(to_opt_string(&chars[305..316])))
                        .admission_date(parse_date(to_opt_string(&chars[419..430])))
                        .resignation_date(parse_date(to_opt_string(&chars[431..441])))
                        .build()
                        .unwrap();

                    members.push(member);

                    window.clear();
                }
            }
            Err(err) if err.kind() == ErrorKind::UnexpectedEof => {
                break;
            }
            Err(err) => {
                return Err(err);
            }
        }
    }

    Ok(members)
}

fn to_opt_string(member_data: &[char]) -> Option<String> {
    let s = member_data.iter().copied().collect::<String>();
    let s = s.trim();
    if s.is_empty() {
        None
    } else {
        Some(s.to_string())
    }
}

fn parse_date(date_str: Option<String>) -> Option<Date<Utc>> {
    date_str
        .filter(|s| s != "00.00.0000")
        .map(|s| NaiveDate::parse_from_str(&s, "%d.%m.%Y").ok())
        .flatten()
        .map(|d| Date::from_utc(d, Utc))
}

#[cfg(test)]
mod tests {
    use super::*;
    use async_std::fs::File;
    use async_std::io::Result;

    #[async_std::test]
    async fn should_parse_spg_test_club() -> Result<()> {
        let file = Box::pin(File::open("tests/TESmitgl.dat").await?);
        let members = parse(file).await?;

        let names = members
            .iter()
            .filter_map(|m| m.last_name.as_ref())
            .collect::<Vec<_>>();

        assert_eq!(
            names,
            vec!["Altmeier", "Lippenlos", "Engelchen", "Mustermann"]
        );

        Ok(())
    }
}
