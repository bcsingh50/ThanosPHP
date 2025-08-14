# Thanos Snap PHP

A casual (but dangerous) package that will let Thanos snap fingers and delete random files inside a directory.

Based off thanos-js

## ⚠️ WARNING
This tool **deletes files**.  
This will delete files unless you use: --dry-run 
This should not delete files in: 
- .git
- vendor
- .env
- composer.json

Warning... this has not really been tested. I ran it locally twice on a windows machine, use at your own risk! 


## Install
```bash
composer require bcsingh50/thanos-php
```

## Run (Via CLI) 
```bash
vendor/bin/snap --dry-run

vendor/bin/snap
```

## Run (Via PHP) 
```bash
ob_start(); // optional to see output. 
$gauntlet->snap($path, $dryRun);
$output = ob_get_clean();

echo $output;
```

## Future improvements
- Add --with-gauntlet flag, so default run without flag does nothing. 
- Add logging and better output so you do not have to capture output from echos. 
