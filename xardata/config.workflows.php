<?php

return [
    'cd_loans' => [
        'type' => 'workflow',
        //'marking_store' => [
        //    'type' => 'method',
        //    'property' => 'marking'
        //],
        //'metadata' => [],
        'supports' => ['cdcollection'],  // DynamicData Object this workflow should apply to
        //'events_to_dispatch' => [],
        'initial_marking' => 'available',
        'places' => [
            'available',
            'requested',
            'approved',
            'rejected',
            'escalated',
            'acknowledged',
            'retrieved',
            'returned',
            'not available'
        ],
        'transitions' => [
            'request' => [
                // See https://github.com/symfony/symfony/blob/6.3/src/Symfony/Bundle/FrameworkBundle/DependencyInjection/FrameworkExtension.php#L917
                // we mean from ANY here, not from ALL like default for workflow
                'from' => ['available', 'requested', 'rejected', 'escalated', 'acknowledged', 'returned'],
                'to' => ['requested']
            ],
            'approve' => [
                'from' => 'requested',
                'to' => 'approved'
            ],
            'retrieve' => [
                'from' => 'approved',
                'to' => ['retrieved', 'not available']
            ],
            'reject' => [
                'from' => 'requested',
                'to' => 'rejected'
            ],
            'acknowledge' => [
                'from' => 'rejected',
                'to' => 'acknowledged'
            ],
            'escalate' => [
                'from' => 'requested',
                'to' => 'escalated'
            ],
            'timeout' => [
                'from' => 'escalated',
                'to' => 'rejected'
            ],
            'return' => [
                'from' => 'retrieved',
                'to' => ['returned', 'available']
            ],
        ],
    ]
];
