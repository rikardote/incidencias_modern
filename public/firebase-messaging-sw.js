importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

// REPLACE WITH YOUR FIREBASE CONFIG
firebase.initializeApp({
  apiKey: "AIzaSyB-3P7ZUyDmdFu5_rvSUApXJ4laYedp5F0",
  authDomain: "incidencias-327fa.firebaseapp.com",
  projectId: "incidencias-327fa",
  storageBucket: "incidencias-327fa.firebasestorage.app",
  messagingSenderId: "657141351778",
  appId: "1:657141351778:web:6383a383db747621e4983b",
  measurementId: "G-XBMT7QKVT1"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  // No llamamos a showNotification aquí porque Firebase ya muestra 
  // automáticamente la notificación si el payload tiene la llave 'notification'.
  // Al llamarlo extra aquí, se duplicaba en algunos navegadores.
});
