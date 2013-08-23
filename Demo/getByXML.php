<?php
error_reporting(E_ALL);
require_once '../Arn/arn.php';

try{
    $xml_string = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<ArnRequest>
    <Availability DisplayCurrency=\"USD\" SearchTimeout=\"15\">
        <HotelAvailability InDate=\"".date('Y-m-d')."\" OutDate=\"".date('Y-m-d', strtotime('tomorrow'))."\" Rooms=\"1\" Adults=\"2\" Children=\"0\">
            <Hotel HotelID=\"274659\"/>
        </HotelAvailability>
    </Availability>
</ArnRequest>";

    $result = Arn::getByXML($xml_string);

    print_r($result);
}
catch (Arn_Error $error) {
    print_r($error->getMessage());
    echo "<hr>";
    print_r($error->getErrorsDetails());
}