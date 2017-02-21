<?php

namespace EVEMail\Jobs;

use Carbon\Carbon;
use EVEMail\Token;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use EVEMail\Http\Controllers\EVEController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class UpdateMetaData implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $token, $data, $eve, $mail_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Token $token, $mail_id, $data)
    {
        $this->eve = new EVEController();
        $this->mail_id = $mail_id;
        $this->data = $data;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->eve->update_mail_header($this->token, $this->mail_id, $this->data);
    }
}
