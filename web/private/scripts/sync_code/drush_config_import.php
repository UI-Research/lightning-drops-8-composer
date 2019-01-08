<?php
// Import all config changes.

//Clear all cache
echo "Rebuilding cache.\n";
passthru('drush cr');
echo "Rebuilding cache complete.\n";

// Update DB
echo "Updating DB . . .\n";
passthru('drush -y updatedb');
echo "Updating DB complete.\n";

// Update Entities
echo "Updating entities . . .\n";
passthru('drush -y entup');
echo "Updating entities complete.\n";

// Import Config
echo "Importing configuration from yml files...\n";
passthru('drush -y config-import');
echo "Import of configuration complete.\n";

//Clear all cache again
echo "Rebuilding cache.\n";
passthru('drush cr');
echo "Rebuilding cache complete.\n";
