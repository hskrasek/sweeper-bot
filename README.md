# SweeperBot

## Requires

* PHP 7.1 or greater
* A Slack App, with a incoming webhook established for a channel
* A Bungie.net application, to get an API Key
* Composer

## Installation

1. Git clone this repository to somewhere that has a Crontab
2. Run the following command after the git clone, within the project directory

    ```bash
    composer install
    ```
3. Update `milestones.php` with your Slack Incoming webhook and Bungie.net API Key
4. Edit your crontab and add the following to post milestones every Tuesday at 10am system time (whatever your server is set to)

    ```bash
    0 10 * * 2 php /path/to/project/folder/milestones.php >/dev/null 2>&1
    ```
