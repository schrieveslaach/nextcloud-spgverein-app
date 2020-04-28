mod opts;

use crate::opts::Opt;
use async_std::io;
use async_std::io::prelude::*;
use oxidized_mdf::MdfDatabase;
use structopt::StructOpt;

#[async_std::main]
async fn main() -> std::io::Result<()> {
    let opt = Opt::from_args();

    let stdin = Box::pin(io::stdin());
    let database = MdfDatabase::from_read(stdin).await?;

    let mut stdout = io::stdout();
    stdout
        .write_all(database.database_name().as_bytes())
        .await?;

    Ok(())
}
