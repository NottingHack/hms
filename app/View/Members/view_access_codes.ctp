<!-- File: /app/View/Member/view_access_codes.ctp -->

<?php
$this->Html->addCrumb('Access Codes', '/members/viewAccessCodes');
?>

<dl>
    <dt>
        Street Door Code
    </dt>
    <dd>
        <?php echo $accessCodes['outerDoorCode']; ?>
    </dd>
    <dt>
        Inner Door Code
    </dt>
    <dd>
        <?php echo $accessCodes['innerDoorCode']; ?>
    </dd>
    <dt>
        Wifi SSID
    </dt>
    <dd>
        <?php echo $accessCodes['wifiSsid']; ?>
    </dd>
    <dt>
        Wifi PSK
    </dt>
    <dd>
        <?php echo $accessCodes['wifiPass']; ?>
    </dd>
</dl>