# FileSupervisor
FileSupervisor is a simple tool that allows you to easily detect new and modified files in your collection.

## FileSupervisor Class
### Constructor
```php
$fs = new FileSupervisor(string $statusFileName, Array $fileNameRegExps);
```
* `$statusFileName` `string` - path to CSV file with current checksums of files to be checked. This file will be overwritten during the execution of checking.
* `Array $fileNameRegExps` `Array of Objects` - object structure
```json
{
  "path":".",
  "file":"filename"
}
```
path - path to search in
file - regex representing the file name 

### Public Methods
* `runCheck()` - runs the check
* `resultToFile(string $fname='result.csv')` - saves the results of checking into a single CSV file with specified name/location.
