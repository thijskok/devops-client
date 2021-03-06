<?php

namespace TestMonitor\DevOps\Actions;

use TestMonitor\DevOps\Resources\Attachment;
use TestMonitor\DevOps\Transforms\TransformsAttachments;

trait ManagesAttachments
{
    use TransformsAttachments;

    /**
     * Add a new attachment.
     *
     * @param string $path
     * @param string $workItemId
     *
     * @param string $projectId
     * @return \TestMonitor\DevOps\Resources\Attachment
     */
    public function addAttachment(string $path, string $workItemId, string $projectId): Attachment
    {
        // First, upload the file...
        $response = $this->post(
            "{$projectId}/_apis/wit/attachments",
            [
                'headers' => ['Content-Type' => 'application/octet-stream'],
                'query' => ['fileName' => basename($path), 'api-version' => $this->apiVersion],
                'body' => fopen($path, 'r'),
            ]
        );

        // ...then, attach it to the work item
        $this->patch(
            "{$projectId}/_apis/wit/workitems/{$workItemId}",
            [
                'headers' => ['Content-Type' => 'application/json-patch+json'],
                'json' => $this->toDevOpsAttachment($response['url']),
            ]
        );

        return $this->fromDevOpsAttachment($response);
    }
}
