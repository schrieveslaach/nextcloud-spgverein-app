use async_std::{
    fs::File,
    io::{stdin, Read, Result as IOResult},
};
use std::path::PathBuf;
use std::pin::Pin;
use std::str::FromStr;
use structopt::StructOpt;

#[derive(Debug)]
pub enum SpgFileVersion {
    V3,
    V4,
}

impl FromStr for SpgFileVersion {
    type Err = String;

    fn from_str(s: &str) -> Result<Self, Self::Err> {
        match s {
            "3" => Ok(SpgFileVersion::V3),
            "4" => Ok(SpgFileVersion::V4),
            _ => Err(format!("Cannot parse {} as file version", s)),
        }
    }
}

#[derive(StructOpt, Debug)]
#[structopt(name = "spg-parser")]
pub struct Opt {
    /// The SPG version that generated the file content
    #[structopt(short = "v")]
    pub file_version: SpgFileVersion,
    /// The path to the file that should be parsed.
    #[structopt(short = "f")]
    pub file_path: Option<PathBuf>,
}

impl Opt {
    pub async fn read(&self) -> IOResult<Pin<Box<dyn Read>>> {
        match &self.file_path {
            Some(file_path) => Ok(Box::pin(File::open(file_path).await?)),
            None => Ok(Box::pin(stdin())),
        }
    }
}
