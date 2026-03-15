<div
    x-data="notificationContainer()"
    @notify.window="addNotification($event.detail)"
    class="fixed top-4 right-4 z-[9999] max-w-md space-y-3 pointer-events-none"
>
    <template x-for="notification in notifications" :key="notification.id">
        <div
            :class="{
                'pointer-events-auto flex items-start gap-3 p-4 rounded-lg shadow-lg border animate-slide-in relative': true,
                'bg-green-50 border-green-200': notification.type === 'success',
                'bg-red-50 border-red-200': notification.type === 'error',
                'bg-yellow-50 border-yellow-200': notification.type === 'warning',
                'bg-blue-50 border-blue-200': notification.type === 'info',
            }"
            x-transition
            @click="removeNotification(notification.id)"
        >
            <!-- Icon -->
            <div class="flex-shrink-0 mt-0.5">
                <template x-if="notification.type === 'success'">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </template>
                <template x-if="notification.type === 'error'">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </template>
                <template x-if="notification.type === 'warning'">
                    <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </template>
                <template x-if="notification.type === 'info'">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </template>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <h3
                    :class="[
                        'font-semibold text-sm',
                        {
                            'text-green-900': notification.type === 'success',
                            'text-red-900': notification.type === 'error',
                            'text-yellow-900': notification.type === 'warning',
                            'text-blue-900': notification.type === 'info',
                        }
                    ]"
                    x-text="notification.title"
                ></h3>
                <template x-if="notification.description">
                    <p
                        :class="[
                            'text-sm mt-1',
                            {
                                'text-green-700': notification.type === 'success',
                                'text-red-700': notification.type === 'error',
                                'text-yellow-700': notification.type === 'warning',
                                'text-blue-700': notification.type === 'info',
                            }
                        ]"
                        x-text="notification.description"
                    ></p>
                </template>
            </div>

            <!-- Close Button -->
            <button
                @click.stop="removeNotification(notification.id)"
                class="flex-shrink-0 text-gray-400 hover:text-gray-500 transition-colors"
                :aria-label="'Close ' + notification.type + ' notification'"
            >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>

            <!-- Progress Bar -->
            <div
                class="absolute bottom-0 left-0 h-1 rounded-full"
                :class="{
                    'bg-green-600': notification.type === 'success',
                    'bg-red-600': notification.type === 'error',
                    'bg-yellow-600': notification.type === 'warning',
                    'bg-blue-600': notification.type === 'info',
                }"
                :style="`animation: shrink ${notification.duration}ms linear forwards;`"
            ></div>
        </div>
    </template>

    <style>
        @keyframes slide-in {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes shrink {
            from {
                width: 100%;
            }
            to {
                width: 0%;
            }
        }

        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }
    </style>

    <script>
        function notificationContainer() {
            return {
                notifications: [],
                nextId: 0,

                addNotification(data) {
                    const notification = {
                        id: this.nextId++,
                        type: data.type || 'info',
                        title: data.title,
                        description: data.description || null,
                        duration: data.duration || 4000,
                    };

                    this.notifications.push(notification);

                    // Auto remove after duration
                    setTimeout(() => {
                        this.removeNotification(notification.id);
                    }, notification.duration);
                },

                removeNotification(id) {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                },
            };
        }
    </script>
</div>
