<?php
$service = file_get_contents(__DIR__ . '/../component/admin/src/Service/CoreIntegrationService.php');
$helper = file_get_contents(__DIR__ . '/../component/admin/src/Helper/CoreUiHelper.php');
$installer = file_get_contents(__DIR__ . '/../package/script.php');
$site = file_get_contents(__DIR__ . '/../component/site/src/Service/RegistrationService.php');

foreach (['com_decaroevents', 'xdecaro\\Core\\Version', 'EntityReference', 'RelationReference', '1.3.0'] as $marker) {
    if (strpos($service, $marker) === false) {
        fwrite(STDERR, "Missing service marker: {$marker}\n");
        exit(1);
    }
}

foreach (['xdecaro\\Core\\Asset\\AssetService', 'xdecaro\\Core\\Version', '1.3.0'] as $marker) {
    if (strpos($helper, $marker) === false) {
        fwrite(STDERR, "Missing helper marker: {$marker}\n");
        exit(1);
    }
}

foreach (['final class pkg_decaroeventsInstallerScript', 'pkg_xdecarocore', 'xdecaro\\Core\\Version', '1.3.0', 'return false;'] as $marker) {
    if (strpos($installer, $marker) === false) {
        fwrite(STDERR, "Missing installer marker: {$marker}\n");
        exit(1);
    }
}
if (strpos($installer, 'class PkgDecaroeventsInstallerScript') !== false) {
    fwrite(STDERR, "Incorrect legacy package installer class name remains\n");
    exit(1);
}

foreach (['FOR UPDATE', 'transactionStart', 'waitlist'] as $marker) {
    if (strpos($site, $marker) === false) {
        fwrite(STDERR, "Missing registration marker: {$marker}\n");
        exit(1);
    }
}

echo "Core/events smoke OK\n";
