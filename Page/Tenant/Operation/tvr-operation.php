<?php
include '../../../Class/tenant_voice_class.php';
$tenantVoiceResponse = new TenantVoiceResponse();

$voiceResponseId = $_GET['id'];
$task = $_GET['task'];
$url = $_GET['url'];

$tenantVoiceResponse->tenantVoiceResponseOperation($voiceResponseId);

header("location: $url");