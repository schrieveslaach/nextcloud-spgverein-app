use crate::{Member, MemberBuilder};
use async_std::io::Read;
use async_std::stream::StreamExt;
use chrono::{Date, Utc};
use color_eyre::eyre::Result;
use oxidized_mdf::{MdfDatabase, Value};
use std::pin::Pin;

pub async fn parse(read: Pin<Box<dyn Read>>) -> Result<Vec<Member>> {
    let mut db = MdfDatabase::from_read(Box::new(read)).await?;
    let mut members = Vec::new();

    let mut rows = match db.rows("tbl_Mitglied") {
        Some(rows) => rows,
        None => {
            return Ok(Vec::new());
        }
    };

    while let Some(row) = rows.next().await {
        let id = row.value("MitgliedID").unwrap();

        let member = MemberBuilder::default()
            .id(id.to_string().parse::<usize>().unwrap())
            .salutation(to_opt_string(row.value("Anrede")))
            .title(to_opt_string(row.value("Titel")))
            .last_name(to_opt_string(row.value("Nachname")))
            .first_name(to_opt_string(row.value("Vorname")))
            .street(to_opt_string(row.value("Strasse")))
            .zipcode(to_opt_string(row.value("PLZ")))
            .city(to_opt_string(row.value("Ort")))
            .birth(to_opt_date(row.value("Geburtsdatum")))
            .admission_date(to_opt_date(row.value("Eintritt_Datum")))
            .resignation_date(to_opt_date(row.value("Austritt_Datum")))
            .build()
            .unwrap();

        members.push(member);
    }

    Ok(members)
}

fn to_opt_string(v: Option<&Value>) -> Option<String> {
    match v? {
        Value::String(s) => Some(s.to_string()),
        _ => None,
    }
}

fn to_opt_date(v: Option<&Value>) -> Option<Date<Utc>> {
    match v? {
        Value::DateTime(datetime) => Some(datetime.date()),
        _ => None,
    }
}

#[cfg(test)]
mod tests {
    use super::*;
    use async_std::fs::File;
    use color_eyre::eyre::Result;

    #[async_std::test]
    async fn should_parse_spg_test_club() -> Result<()> {
        let file = Box::pin(File::open("tests/spg_verein_TST.mdf").await?);
        let members = parse(file).await?;

        let names = members
            .iter()
            .filter_map(|m| m.last_name.as_ref())
            .collect::<Vec<_>>();

        assert_eq!(
            names,
            vec![
                "Bergmann",
                "Kanter",
                "König",
                "König",
                "König",
                "Müller",
                "Münchhausen",
                "Waldmeister",
                "Neumann",
                "Meier",
                "Schulze",
                "Beckenbauer",
                "Schmitz"
            ]
        );

        Ok(())
    }
}
