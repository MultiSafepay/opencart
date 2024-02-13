#!/bin/bash
set -e

# Get the script's directory
get_script_directory() {
    DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
}

# Display the warning message
display_warning_message() {
    echo
    printf "%2sNOTE: If you have issues getting the right PHP version,\n" ""
    printf "%8srefresh your shell using some of the commands:\n" ""
    printf "%12sexec bash -l\n" ""
    printf "%12ssource ~/.bash_profile\n" ""
    printf "%12ssource ~/.bashrc\n" ""
}

# Function to check if PHP is installed
check_php() {
    if ! command -v php &> /dev/null
    then
        echo "PHP could not be found. Please install PHP first."
        exit
    fi
}

# Function to get the PHP version
get_php_version() {
    IS_USER_DEFINED=false
    if [[ -n "$PHP_VERSION_ARG" ]]; then
        PHP_VERSION=$PHP_VERSION_ARG
        IS_USER_DEFINED=true
    else
        while true; do
            # Get the exact PHP version
            PHP_VERSION=$(php -r "echo PHP_VERSION;")
            # Check if PHP_VERSION is empty or not numeric
            if [[ -z "$PHP_VERSION" || ! $PHP_VERSION =~ ^[0-9]+(\.[0-9]+)*$ ]]; then
                printf "%2sError: PHP_VERSION format is empty or not valid.\n" ""
                printf "%5sPlease enter (without quotes) the major PHP version manually:  '7' or '8' or 'exit' to quit:\n" ""
                echo
                printf "%5sYour choice is: "
                read -r USER_INPUT
                if [[ $USER_INPUT == 7 || $USER_INPUT == 8 ]]; then
                    PHP_VERSION=$USER_INPUT
                    IS_USER_DEFINED=true
                elif [[ $USER_INPUT == "exit" ]]; then
                    echo "Exiting the script."
                    exit 0
                else
                    echo "Invalid input. Please enter without quotes '7', '8' or 'exit'."
                    continue
                fi
            fi
            # Split the PHP version into major part (7 or 8 in this case)
            PHP_MAJOR_VERSION_FROM_SHELL=$(echo "$PHP_VERSION" | cut -d. -f1)
            break
        done
    fi
}

# Function to handle the PHP version argument
handle_php_version_arg() {
    if [[ $IS_USER_DEFINED == true ]]; then
        PHP_MAJOR_VERSION=$PHP_VERSION
    else
        while true; do
            # Ask the user for the PHP version
            echo
            printf "%2sPlease choose the option (1, 2, 3 or 4):\n" ""
            printf "%5s1 Use composer.json for PHP 7.x\n" ""
            printf "%5s2 Use composer.json for PHP 8.x\n" ""
            printf "%5s3 Use composer.json taking the PHP version $( $IS_USER_DEFINED && echo "from the previous user input" || echo "from your shell" )\n" ""
            printf "%5s4 Exit the script\n" ""
            echo
            printf "%5sYour choice is: "
            read -r USER_CHOICE
            echo ""

            # Check the user's choice
            case $USER_CHOICE in
                1)
                    printf "%1s... so you chose PHP 7.x\n" ""
                    PHP_MAJOR_VERSION=7
                    break
                    ;;
                2)
                    printf "%1s... so you chose PHP 8.x\n" ""
                    PHP_MAJOR_VERSION=8
                    break
                    ;;
                3)
                    printf "%1s... so you are using the major PHP version $( $IS_USER_DEFINED && echo "from the first user input" || echo "detected from the shell" ) which is $PHP_MAJOR_VERSION_FROM_SHELL\n" ""
                    PHP_MAJOR_VERSION=$PHP_MAJOR_VERSION_FROM_SHELL
                    break
                    ;;
                4)
                    printf "%2sExiting the script\n" ""
                    echo
                    exit 0
                    ;;
                *)
                    printf "%2sInvalid choice. Please choose 1, 2, 3, or 4\n" ""
                    ;;
            esac
        done
    fi
}

# Function to copy the correct composer.json file
copy_composer_json() {
    echo
    # Check if the PHP version is 8 or higher
    if [[ $PHP_MAJOR_VERSION -ge 8 ]]; then
        printf "%5s- Copying composer-php-8.json to composer.json ...\n" ""
        cp "$DIR/data/composer-php-8.json" "$DIR/../composer.json"
    else
        printf "%5s- Copying composer-php-7.json to composer.json ...\n" ""
        cp "$DIR/data/composer-php-7.json" "$DIR/../composer.json"
    fi
}

# Function to handle composer.lock and run composer
handle_composer() {
    # Remove composer.lock if it exists
    if [[ -f "$DIR/../composer.lock" ]]; then
        printf "%5s- Removing composer.lock ...\n" ""
        rm "$DIR/../composer.lock"
    fi
    # Check if composer.lock was successfully removed
    if [[ ! -f "$DIR/../composer.lock" ]]; then
        # Run Composer install if composer.lock was successfully removed
        printf "%5s- Running composer install $COMPOSER_DEV_ARG ...\n" ""
        echo
        cd "$DIR/.." && composer install $COMPOSER_DEV_ARG
    else
        # Run Composer update if composer.lock was not successfully removed
        printf "%5s- Running composer update $COMPOSER_DEV_ARG ...\n" ""
        echo
        cd "$DIR/.." && composer update $COMPOSER_DEV_ARG
    fi
}

# Main script
PHP_VERSION_ARG=$1
COMPOSER_DEV_ARG=${2:-""}
get_script_directory
display_warning_message
check_php
get_php_version
handle_php_version_arg
copy_composer_json
handle_composer
