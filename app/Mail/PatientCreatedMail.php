<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PatientCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $patient;
    public $creator;

    /**
     * Create a new message instance.
     *
     * @param $patient
     * @param $creator
     */
    public function __construct($patient, $creator)
    {
        $this->patient = $patient;
        $this->creator = $creator;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Patient Created')
                    ->view('emails.patient_created');
    }
}
