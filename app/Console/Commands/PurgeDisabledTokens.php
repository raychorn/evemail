<?php

namespace EVEMail\Console\Commands;

use Mail;
use EVEMail\User;
use EVEMail\Token;
use EVEMail\UserEmail;
use EVEMail\MailLabel;
use EVEMail\MailBody;
use EVEMail\MailHeader;
use EVEMail\MailHeaderUpdate;
use EVEMail\Http\Controllers\MailController;
use Illuminate\Console\Command;

class PurgeDisabledTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:purge_disabled_tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge Disabled Tokens from Database.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->mail = new MailController();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $disabled_tokens = Token::where('disabled', 1)->get();
        $now = Carbon::now();
        foreach ($disabled_tokens as $token) {
            $now = $now->addSeconds(13);
            $user = User::where('character_id', $token->character_id)->first();
            $payload = [
                'recipients' => [
                    [
                        'recipient_id' => $user->character_id,
                        'recipient_type' => "character"
                    ],
                ],
                'subject' => "EVEMail Alert: Your Account has been disabled",
                'body' => "Hello {$user->character_name},<br /><br />This is just a friendly EVEMail from EVEMail.Space. We wanted to inform you that our system has disabled your SSO Token. This is probably due to an invalid response that we received from CCP while attempting to update your inbox on our system. In the event that this was not suppose to happen, you can quickly reactivate your token and service be resumed by logging into the application at anytime.<br /><br /><a href=\"https://www.evemail.space\">EVEMail.Space</a><br /><br />Thank You,<br />EVEMail Admin<br /><br />**This was an automated EVEMail sent by the EVEMail.Space System.**",
                'approved_cost' => 100000
            ];
            $this->mail->send_message(config('services.eve.evemail_admin_char_id'), $payload, $now);

            MailHeaderUpdate::where('character_id', $disabled_token->character_id)->delete();
            MailHeader::where('character_id', $disabled_token->character_id)->delete();
            MailBody::where('character_id', $disabled_token->character_id)->delete();
            MailLabel::where('character_id', $disabled_token->character_id)->delete();
            UserEmail::where('character_id', $disabled_token->character_id)->delete();
            User::where('character_id', $disabled_token->character_id)->delete();
            Token::where('character_id', $disabled_token->character_id)->delete();
        }

    }
}
