mod member;
mod opts;
mod v3;

use crate::member::{Member, MemberBuilder};
use crate::opts::{Opt, SpgFileVersion};
use async_std::fs::File;
use async_std::io::prelude::*;
use async_std::io::{self, Read, Result};
use oxidized_mdf::MdfDatabase;
use std::pin::Pin;
use structopt::StructOpt;

#[async_std::main]
async fn main() -> std::io::Result<()> {
    let opt = Opt::from_args();

    let file = Box::pin(File::open(opt.file_path).await?);

    let members = match opt.file_version {
        SpgFileVersion::V3 => v3::parse(file).await?,
        SpgFileVersion::V4 => {
            parse_v4(file).await?;
            todo!()
        }
    };

    let mut out = io::stdout();
    out.write_all(b"[").await?;
    for (i, m) in members.iter().enumerate() {
        if i > 0 {
            out.write_all(b",").await?;
        }
        let json = serde_json::to_string(&m)?;
        out.write_all(json.as_bytes()).await?;
    }
    out.write_all(b"]").await?;

    Ok(())
}

async fn parse_v4(read: Pin<Box<dyn Read>>) -> Result<()> {
    let database = MdfDatabase::from_read(read).await?;

    let mut stdout = io::stdout();
    stdout
        .write_all(database.database_name().as_bytes())
        .await?;

    Ok(())
}
