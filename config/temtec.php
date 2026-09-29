<?php

return [
    // Comma-separated list of addresses that receive new service requests.
    'sales_emails' => array_values(array_filter(array_map('trim', explode(',', (string) env('SALES_EMAILS', ''))))),
];
