// /firebase-messaging-sw.js

// Import the Firebase scripts
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');

// Your Firebase configuration
const firebaseConfig = {
        apiKey: "AIzaSyA_IjKrHD9xUpV0NRBuNGjzA5DD0MvCEJE",
        authDomain: "seejobrun.firebaseapp.com",
        projectId: "seejobrun",
        storageBucket: "seejobrun.firebasestorage.app",
        messagingSenderId: "718155099941",
        appId: "1:718155099941:web:3646c79607bd1e4af074cc",
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Initialize Firebase Messaging
const messaging = firebase.messaging();
function requestPermission() {
    //console.log('Requesting permission...');
    Notification.requestPermission().then((permission) => {
      if (permission === 'granted') {
       // console.log('Notification permission granted.');
      }
    });
  }
// Background message handler (will be triggered when the app is in the background)
messaging.onBackgroundMessage((payload) => {
    

    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/icon.png', // Customize your icon path here
    };

    // Show notification
    self.registration.showNotification(notificationTitle, notificationOptions);
});


