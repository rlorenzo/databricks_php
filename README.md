# Connecting to Databricks via PHP

Example of connecting to [Databricks](https://databricks.com) using PHP in a Docker environment

## Requirements

Make sure you have [Docker Desktop](https://www.docker.com/products/docker-desktop) installed.

## Usage

1. Copy odbc.ini.dist
   - `cp -p odbc.ini.dist odbc.ini`
2. Edit odbc.ini and replace the values in it with your Databricks SQL endpoint connection details
   - Replace <SQL_ENDPOINT_SERVER> and <SQL_ENDPOINT_HTTPPATH> with values found under the "Connection details" tab for your Databricks SQL endpoint.
   - Replace <PERSONAL_TOKEN> with the your account's [Personal Access Token](https://docs.microsoft.com/en-us/azure/databricks/administration-guide/access-control/tokens).
3. Copy config-dist.php
   - `cp -p config-dist.php config.php`
4. Edit config.php and replace <PERSONAL_TOKEN> with the your account's [Personal Access Token](https://docs.microsoft.com/en-us/azure/databricks/administration-guide/access-control/tokens).
5. Edit line 21 of test_connection.php to be a valid SELECT query.
6. Build the container
   - `docker build --platform linux/amd64 -t databricks_php .`
7. Run the test connection script
   -  `docker run databricks_php`
