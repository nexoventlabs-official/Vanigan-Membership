<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CreateLoanFlow extends Command
{
    protected $signature = 'whatsapp:create-loan-flow';
    protected $description = 'Create and publish the Vanigan Loan Application WhatsApp Flow';

    public function handle()
    {
        $token = config('whatsapp.access_token');
        $wabaId = config('whatsapp.waba_id');
        $appUrl = config('app.url');
        $api = 'v21.0';
        if (!$token || !$wabaId) { $this->error('Missing credentials'); return 1; }

        $endpointUri = rtrim($appUrl, '/') . '/webhook/flow';
        $flowName = 'Vanigan Loan Flow ' . time();

        $this->info("Creating: {$flowName}");
        $res = Http::withToken($token)->post("https://graph.facebook.com/{$api}/{$wabaId}/flows", [
            'name' => $flowName, 'categories' => ['OTHER'], 'endpoint_uri' => $endpointUri,
        ]);
        if (!$res->successful()) { $this->error($res->body()); return 1; }
        $flowId = $res->json('id');
        $this->info("Created: {$flowId}");

        $this->info('Uploading JSON...');
        $flowJson = $this->buildFlowJSON();
        $tmp = tempnam(sys_get_temp_dir(), 'flow_');
        file_put_contents($tmp, json_encode($flowJson));
        $res = Http::withToken($token)
            ->attach('file', file_get_contents($tmp), 'flow.json', ['Content-Type' => 'application/json'])
            ->post("https://graph.facebook.com/{$api}/{$flowId}/assets", ['name' => 'flow.json', 'asset_type' => 'FLOW_JSON']);
        unlink($tmp);
        $this->info('Warnings: ' . json_encode($res->json('validation_errors') ?? []));

        $this->info('Publishing...');
        $res = Http::withToken($token)->post("https://graph.facebook.com/{$api}/{$flowId}/publish");
        if ($res->successful() && $res->json('success')) {
            $this->info("PUBLISHED! WHATSAPP_LOAN_FLOW_ID={$flowId}");
        } else {
            $this->warn('Publish: ' . $res->body());
            $this->info("DRAFT. WHATSAPP_LOAN_FLOW_ID={$flowId}");
        }
        return 0;
    }

    protected function buildFlowJSON(): array
    {
        $ex = ['type' => 'string', '__example__' => 'x'];
        $exArr = fn($e) => ['type' => 'array', 'items' => ['type' => 'object', 'properties' => ['id' => ['type' => 'string'], 'title' => ['type' => 'string'], 'description' => ['type' => 'string'], 'image' => ['type' => 'string']]], '__example__' => [$e]];

        return [
            'version' => '6.0',
            'data_api_version' => '3.0',
            'routing_model' => [
                'BUSINESS_TYPE' => ['BUSINESS_NAME'],
                'BUSINESS_NAME' => [],
            ],
            'screens' => [
                // 1. Select Business Type (with icons)
                ['id' => 'BUSINESS_TYPE', 'title' => 'Select Business Type',
                    'data' => [
                        'business_types' => $exArr(['id' => 'pvt_ltd', 'title' => 'Pvt Ltd', 'description' => 'Private Limited']),
                        'flow_token' => $ex,
                    ],
                    'layout' => ['type' => 'SingleColumnLayout', 'children' => [
                        ['type' => 'TextSubheading', 'text' => 'Select your Business Type'],
                        ['type' => 'RadioButtonsGroup', 'name' => 'business_type', 'label' => 'Business Type', 'required' => true, 'data-source' => '${data.business_types}'],
                        ['type' => 'Footer', 'label' => 'Continue', 'on-click-action' => ['name' => 'data_exchange', 'payload' => ['screen' => 'BUSINESS_TYPE', 'business_type' => '${form.business_type}', 'flow_token' => '${data.flow_token}']]],
                    ]]],
                // 2. Enter Business Name
                ['id' => 'BUSINESS_NAME', 'title' => 'Business Name', 'terminal' => true, 'success' => true,
                    'data' => [
                        'business_type' => $ex,
                        'flow_token' => $ex,
                    ],
                    'layout' => ['type' => 'SingleColumnLayout', 'children' => [
                        ['type' => 'TextSubheading', 'text' => 'Enter your Business Name'],
                        ['type' => 'TextInput', 'name' => 'business_name', 'label' => 'Business Name', 'required' => true, 'input-type' => 'text', 'helper-text' => 'Enter your registered business name'],
                        ['type' => 'Footer', 'label' => 'Submit Application', 'on-click-action' => ['name' => 'data_exchange', 'payload' => ['screen' => 'BUSINESS_NAME', 'business_name' => '${form.business_name}', 'business_type' => '${data.business_type}', 'flow_token' => '${data.flow_token}']]],
                    ]]],
            ],
        ];
    }
}
