var devEnv = false;

// TESTING ON IIS SERVER
if (document.location.host.includes("localhost")) {
    devEnv = true;
}

// TESTING ON VS CODE LIVE SERVER, LARAVEL SERV
if (document.location.host.includes("127")) {
    devEnv = true;
}

// TESTING ON LOCAL NETWORK
if (document.location.host.includes("192")) {
    devEnv = true;
}

// USING FILE SYSTEM
if (document.location.protocol == "file:") {
    devEnv = true;
}

if (!devEnv && document.location.protocol != "https:") {
    document.location = document.URL.replace(/^http:/i, "https:");
}