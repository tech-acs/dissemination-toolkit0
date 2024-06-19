<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationInbox extends Component
{
    public $notifications;
    public $unreadCount;
    public $selectedNotification;

    public function mount()
    {
        $this->notifications = auth()->user()->notifications;
        $this->unreadCount = auth()->user()->unreadNotifications->count();
        $this->selectedNotification = $this->notifications->first();
    }

    public function show($id)
    {
        $this->selectedNotification = auth()->user()->notifications()->where('id', $id)->first();
        $this->selectedNotification->markAsRead();
    }

    public function render()
    {
        return view('livewire.notification-inbox');
    }
}
