<?php
$service=file_get_contents(__DIR__.'/../component/admin/src/Service/CoreIntegrationService.php');$helper=file_get_contents(__DIR__.'/../component/admin/src/Helper/CoreUiHelper.php');$site=file_get_contents(__DIR__.'/../component/site/src/Service/RegistrationService.php');
foreach(['com_decaroevents','EntityReference','RelationReference'] as $x){if(strpos($service,$x)===false){fwrite(STDERR,"Missing $x
");exit(1);}}
foreach(['AssetService','1.1.0'] as $x){if(strpos($helper,$x)===false){fwrite(STDERR,"Missing $x
");exit(1);}}
foreach(['FOR UPDATE','transactionStart','waitlist'] as $x){if(strpos($site,$x)===false){fwrite(STDERR,"Missing $x
");exit(1);}}
echo "Core/events smoke OK
";
