# AppShed Ebs Tools

Currently just 2 simple tools to make it easier to setup scheduled changes to the elastic beanstalk environments

## Install

You need to copy the `config/aws.json.dist` file to `config/aws.json` and insert your keys.

## Usage

### Update

Change the minimum number of instances for the environment

    ./app appshed:ebs:update --min-instances=1 <env name>

### Status

Check the status of your environment, optionally keep checking until its Ready

    ./app appshed:ebs:status <env name> --wait
