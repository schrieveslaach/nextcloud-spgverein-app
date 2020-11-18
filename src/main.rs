mod member;
mod opts;
mod v3;
mod v4;

use crate::member::{Member, MemberBuilder};
use crate::opts::{Opt, SpgFileVersion};
use async_std::prelude::*;
use structopt::StructOpt;

#[async_std::main]
async fn main() -> std::io::Result<()> {
    let opt = Opt::from_args();

    let read = opt.read().await?;

    let mut members = match opt.file_version {
        SpgFileVersion::V3 => v3::parse(read).await?,
        SpgFileVersion::V4 => v4::parse(read).await?,
    };

    members.sort();

    let mut out = async_std::io::stdout();
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
