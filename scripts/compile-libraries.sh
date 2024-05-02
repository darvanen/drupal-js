#!/bin/bash


# Function to install npm packages
install_package() {
   package_name="$1"
   npm install --include=dev "$package_name"
}


# Function to scan for package.json files and install dependencies
scan_and_install() {
   directory="$1"
   echo "Scanning directory: $directory"
   find "$directory" -type f -name "package.json" -not -path "*/node_modules/*" | while read -r file; do
       is_drupal_library=$(jq -r '.["drupal-library"] == true' "$file")
       if [ "$is_drupal_library" == "true" ]; then
           package_name=$(jq -r '.name' "$file")
           if [ ! -z "$package_name" ]; then
               echo "Installing dependencies for package: $package_name"
               install_package "$package_name"
           else
               echo "Error: Could not extract package name from $file"
           fi
       fi
   done
}


# Check if at least one argument is provided
if [ $# -eq 0 ]; then
   echo "Usage: $0 <directory1> [<directory2> ...]"
   exit 1
fi


# Loop through each provided directory and scan for package.json files
for dir in "$@"; do
   scan_and_install "$dir"
done
