use structopt::StructOpt;
use std::str::FromStr;

#[derive(Debug)]
pub enum SpgFileVersion {
    V3,
    V4,
}

impl FromStr for SpgFileVersion {
    type Err = &'static str;

    fn from_str(s: &str) -> Result<Self, Self::Err> {
        todo!()
    }
}

#[derive(StructOpt, Debug)]
#[structopt(name = "spg-parser")]
pub struct Opt {
    pub file_version: SpgFileVersion
}