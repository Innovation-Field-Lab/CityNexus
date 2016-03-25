<!-- resources/views/emails/activate.blade.php -->

To activate you account follow this link: {{ url('/activate-account?key=' . $token) }}