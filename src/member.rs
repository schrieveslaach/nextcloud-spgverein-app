use chrono::{Date, Utc};
use serde::{Serialize, Serializer};

#[derive(derive_builder::Builder, Debug, Serialize)]
#[serde(rename_all = "camelCase")]
pub struct Member {
    id: usize,
    #[builder(default = "None")]
    #[serde(skip_serializing_if = "Option::is_none")]
    related_member_id: Option<usize>,

    #[builder(default = "None")]
    #[serde(skip_serializing_if = "Option::is_none")]
    salutation: Option<String>,
    #[builder(default = "None")]
    #[serde(skip_serializing_if = "Option::is_none")]
    title: Option<String>,
    #[builder(default = "None")]
    #[serde(skip_serializing_if = "Option::is_none")]
    first_name: Option<String>,
    #[builder(default = "None")]
    #[serde(skip_serializing_if = "Option::is_none")]
    last_name: Option<String>,
    #[builder(default = "None")]
    #[serde(skip_serializing_if = "Option::is_none")]
    street: Option<String>,
    #[builder(default = "None")]
    #[serde(skip_serializing_if = "Option::is_none")]
    zipcode: Option<String>,
    #[builder(default = "None")]
    #[serde(skip_serializing_if = "Option::is_none")]
    city: Option<String>,
    #[builder(default = "None")]
    #[serde(
        serialize_with = "serialize_option_date",
        skip_serializing_if = "Option::is_none"
    )]
    birth: Option<Date<Utc>>,

    #[builder(default = "None")]
    #[serde(
        serialize_with = "serialize_option_date",
        skip_serializing_if = "Option::is_none"
    )]
    admission_date: Option<Date<Utc>>,
    #[builder(default = "None")]
    #[serde(
        serialize_with = "serialize_option_date",
        skip_serializing_if = "Option::is_none"
    )]
    resignation_date: Option<Date<Utc>>,
}

fn serialize_option_date<S>(x: &Option<Date<Utc>>, s: S) -> Result<S::Ok, S::Error>
where
    S: Serializer,
{
    s.serialize_some(&x.map(|d| d.to_string()))
}
