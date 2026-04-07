<?php

namespace App\Services;

use App\Models\Domain;

class DomainVerificationService
{
    public function verify(Domain $domain): bool
    {
//         $records = [
//     [
//         'target' => config('tenancy.verification_cname'),
//     ]
// ];

        $records = @dns_get_record($domain->domain, DNS_CNAME) ?: [];

        if (!$records) {
            return false;
        }

        $expected = rtrim(config('tenancy.verification_cname'), '.');
// dd($expected,$records );
        foreach ($records as $record) {
            if (
                isset($record['target']) &&
                rtrim($record['target'], '.') === $expected
            ) {
                return true;
            }
        }

        return false;
    }
}

