<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HouseholdResetPassword extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     */
    public $token;

    /**
     * The WordPress reset URL.
     */
    public $resetUrl;

    /**
     * The callback that should be used to create the reset password URL.
     */
    public static $createUrlCallback;

    /**
     * The callback that should be used to build the mail message.
     */
    public static $toMailCallback;

    /**
     * Create a new notification instance.
     */
    public function __construct($token, $resetUrl = null)
    {
        $this->token = $token;
        $this->resetUrl = $resetUrl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return $this->buildMailMessage($this->resetUrl($notifiable));
    }

    /**
     * Get the reset password notification mail message for the given URL.
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('Reset Password Notification')
            ->line('You are receiving this email because we received a password reset request for your household account.')
            ->action('Reset Password', $url)
            ->line('This password reset link will expire in 60 minutes.')
            ->line('If you did not request a password reset, no further action is required.');
    }

    /**
     * Get the reset URL for the given notifiable.
     */
    protected function resetUrl($notifiable)
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        }

        // Use provided reset URL or fallback to default
        $baseUrl = $this->resetUrl ?: env('WORDPRESS_URL', 'http://wordpress') . '/parish-portal/';
        
        // Ensure the URL ends with proper separator
        $separator = (strpos($baseUrl, '?') !== false) ? '&' : '?';
        
        return $baseUrl . $separator . http_build_query([
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
    }

    /**
     * Set a callback that should be used when creating the reset password button URL.
     */
    public static function createUrlUsing($callback)
    {
        static::$createUrlCallback = $callback;
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
