use chrono::{Date, Utc};
use serde::{Serialize, Serializer};

#[derive(derive_builder::Builder, Debug, Serialize)]
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
