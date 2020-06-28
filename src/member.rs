use chrono::{Date, Utc};
use serde::{Serialize, Serializer};
use std::cmp::{Ord, Ordering};

#[derive(derive_builder::Builder, Debug, Eq, Serialize)]
#[serde(rename_all = "camelCase")]
pub struct Member {
    pub id: usize,
    #[builder(default = "None")]
    #[serde(skip_serializing_if = "Option::is_none")]
    pub related_member_id: Option<usize>,

    #[builder(default = "None")]
    #[serde(skip_serializing_if = "Option::is_none")]
    pub salutation: Option<String>,
    #[builder(default = "None")]
    #[serde(skip_serializing_if = "Option::is_none")]
    pub title: Option<String>,
    #[builder(default = "None")]
    #[serde(skip_serializing_if = "Option::is_none")]
    pub first_name: Option<String>,
    #[builder(default = "None")]
    #[serde(skip_serializing_if = "Option::is_none")]
    pub last_name: Option<String>,
    #[builder(default = "None")]
    #[serde(skip_serializing_if = "Option::is_none")]
    pub street: Option<String>,
    #[builder(default = "None")]
    #[serde(skip_serializing_if = "Option::is_none")]
    pub zipcode: Option<String>,
    #[builder(default = "None")]
    #[serde(skip_serializing_if = "Option::is_none")]
    pub city: Option<String>,
    #[builder(default = "None")]
    #[serde(
        serialize_with = "serialize_option_date",
        skip_serializing_if = "Option::is_none"
    )]
    pub birth: Option<Date<Utc>>,

    #[builder(default = "None")]
    #[serde(
        serialize_with = "serialize_option_date",
        skip_serializing_if = "Option::is_none"
    )]
    pub admission_date: Option<Date<Utc>>,
    #[builder(default = "None")]
    #[serde(
        serialize_with = "serialize_option_date",
        skip_serializing_if = "Option::is_none"
    )]
    pub resignation_date: Option<Date<Utc>>,
}

fn serialize_option_date<S>(x: &Option<Date<Utc>>, s: S) -> Result<S::Ok, S::Error>
where
    S: Serializer,
{
    s.serialize_some(&x.map(|d| d.to_string()))
}

impl Ord for Member {
    fn cmp(&self, other: &Self) -> Ordering {
        let mut c = cmp(self.city.as_ref(), other.city.as_ref());

        if c == Ordering::Equal {
            c = cmp(self.zipcode.as_ref(), other.zipcode.as_ref());

            if c == Ordering::Equal {
                c = cmp(self.street.as_ref(), other.street.as_ref());
                if c == Ordering::Equal {
                    c = self.id.cmp(&other.id)
                }
            }
        }

        c
    }
}

impl PartialOrd for Member {
    fn partial_cmp(&self, other: &Self) -> Option<Ordering> {
        Some(self.cmp(other))
    }
}

impl PartialEq for Member {
    fn eq(&self, other: &Self) -> bool {
        self.cmp(other) == Ordering::Equal
    }
}

fn cmp<T>(a: Option<&T>, b: Option<&T>) -> Ordering
where
    T: Ord,
{
    match (a, b) {
        (Some(a), Some(b)) => a.cmp(b),
        (Some(_), None) => Ordering::Less,
        (None, Some(_)) => Ordering::Greater,
        _ => Ordering::Equal,
    }
}

#[cfg(test)]
mod tests {
    use super::*;

    #[test]
    fn should_sort_member_by_id() {
        let member1 = MemberBuilder::default().id(1).build().unwrap();
        let member2 = MemberBuilder::default().id(2).build().unwrap();

        let mut members = vec![member2, member1];
        members.sort();

        let ids = members.into_iter().map(|m| m.id).collect::<Vec<_>>();
        assert_eq!(ids, vec![1, 2]);
    }

    #[test]
    fn should_sort_member_by_city() {
        let member1 = MemberBuilder::default().id(1).build().unwrap();
        let member2 = MemberBuilder::default()
            .id(2)
            .city(Some("Aachen".to_string()))
            .build()
            .unwrap();

        let mut members = vec![member2, member1];
        members.sort();

        let ids = members.into_iter().map(|m| m.id).collect::<Vec<_>>();
        assert_eq!(ids, vec![2, 1]);
    }

    #[test]
    fn should_sort_member_by_city_and_zipcode() {
        let member1 = MemberBuilder::default()
            .id(1)
            .city(Some("Aachen".to_string()))
            .zipcode(Some("52078".to_string()))
            .build()
            .unwrap();
        let member2 = MemberBuilder::default()
            .id(2)
            .city(Some("Aachen".to_string()))
            .zipcode(Some("52074".to_string()))
            .build()
            .unwrap();

        let mut members = vec![member2, member1];
        members.sort();

        let ids = members.into_iter().map(|m| m.id).collect::<Vec<_>>();
        assert_eq!(ids, vec![2, 1]);
    }

    #[test]
    fn should_sort_member_by_city_and_street() {
        let member1 = MemberBuilder::default()
            .id(1)
            .city(Some("Aachen".to_string()))
            .street(Some("Schillerstraße 3".to_string()))
            .build()
            .unwrap();
        let member2 = MemberBuilder::default()
            .id(2)
            .city(Some("Aachen".to_string()))
            .street(Some("Schillerstraße 4".to_string()))
            .build()
            .unwrap();

        let mut members = vec![member2, member1];
        members.sort();

        let ids = members.into_iter().map(|m| m.id).collect::<Vec<_>>();
        assert_eq!(ids, vec![1, 2]);
    }
}
