/* Minimal client logic for signaling via Laravel Echo.
   Assumes window.Echo is configured elsewhere (Pusher or Laravel WebSockets). */

(function () {
  const callRoot = document.getElementById('call-root');
  const incomingBanner = document.getElementById('incoming-banner');
  if (!callRoot || !incomingBanner) return;

  const localVideo = document.getElementById('localVideo');
  const remoteVideo = document.getElementById('remoteVideo');
  const btnMic = document.getElementById('btn-mic');
  const btnVideo = document.getElementById('btn-video');
  const btnSwitch = document.getElementById('btn-switch');
  const btnEnd = document.getElementById('btn-end');
  const btnAccept = document.getElementById('btn-accept');
  const btnDecline = document.getElementById('btn-decline');
  const btnMinimize = document.getElementById('btn-minimize');

  let pc = null;
  let localStream = null;
  let currentDeviceId = null;
  let currentCallUuid = null;

  function showCallUI() { callRoot.classList.remove('hidden'); }
  function hideCallUI() { callRoot.classList.add('hidden'); }
  function showIncoming() { incomingBanner.classList.remove('hidden'); }
  function hideIncoming() { incomingBanner.classList.add('hidden'); }

  async function getStream(deviceId) {
    const constraints = {
      audio: true,
      video: deviceId ? { deviceId: { exact: deviceId } } : { facingMode: 'user' }
    };
    return navigator.mediaDevices.getUserMedia(constraints);
  }

  function createPeer() {
    const pc = new RTCPeerConnection({ iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] });
    pc.ontrack = (e) => {
      if (remoteVideo) remoteVideo.srcObject = e.streams[0];
    };
    pc.onicecandidate = (e) => {
      // In a real app, send ICE to the other peer via your signaling (Echo)
      // This is a stub; you can extend with presence channels and custom events.
    };
    return pc;
  }

  // Example: listen to private channel for the authenticated user ID
  const userIdMeta = document.head.querySelector('meta[name="user-id"]');
  const userId = userIdMeta ? userIdMeta.content : null;
  if (!userId || !window.Echo) return;

  window.Echo.private('calls.user.' + userId)
    .listen('.calls.incoming', (payload) => {
      currentCallUuid = payload.uuid;
      showIncoming();
    })
    .listen('.calls.accepted', async (payload) => {
      // remote accepted—start WebRTC if you initiated
      await startLocalIfNeeded();
      await ensurePc();
      // You would exchange SDP/ICE here via your own events
    })
    .listen('.calls.declined', () => {
      endCall();
    })
    .listen('.calls.ended', () => {
      endCall();
    });

  async function startLocalIfNeeded() {
    if (!localStream) {
      localStream = await getStream(currentDeviceId);
      localStream.getTracks().forEach(t => pc && pc.addTrack(t, localStream));
      localVideo.srcObject = localStream;
    }
  }

  async function ensurePc() {
    if (!pc) pc = createPeer();
    if (localStream) {
      localStream.getTracks().forEach(t => pc.addTrack(t, localStream));
    }
  }

  function endCall() {
    if (pc) { pc.close(); pc = null; }
    if (localStream) { localStream.getTracks().forEach(t => t.stop()); localStream = null; }
    currentCallUuid = null;
    hideCallUI();
    hideIncoming();
  }

  // UI handlers
  btnAccept && btnAccept.addEventListener('click', async () => {
    if (!currentCallUuid) return;
    await axios.post('/one2one/calls/' + currentCallUuid + '/accept');
    hideIncoming();
    showCallUI();
    await startLocalIfNeeded();
    await ensurePc();
  });

  btnDecline && btnDecline.addEventListener('click', async () => {
    if (!currentCallUuid) return;
    await axios.post('/one2one/calls/' + currentCallUuid + '/decline');
    hideIncoming();
  });

  btnEnd && btnEnd.addEventListener('click', async () => {
    if (!currentCallUuid) return;
    await axios.post('/one2one/calls/' + currentCallUuid + '/end');
    endCall();
  });

  btnMic && btnMic.addEventListener('click', () => {
    if (!localStream) return;
    const enabled = localStream.getAudioTracks().every(t => t.enabled);
    localStream.getAudioTracks().forEach(t => t.enabled = !enabled);
    btnMic.textContent = enabled ? 'Unmute Mic' : 'Mute Mic';
  });

  btnVideo && btnVideo.addEventListener('click', () => {
    if (!localStream) return;
    const enabled = localStream.getVideoTracks().every(t => t.enabled);
    localStream.getVideoTracks().forEach(t => t.enabled = !enabled);
    btnVideo.textContent = enabled ? 'Video On' : 'Video Off';
  });

  btnSwitch && btnSwitch.addEventListener('click', async () => {
    const devices = await navigator.mediaDevices.enumerateDevices();
    const cams = devices.filter(d => d.kind === 'videoinput');
    if (!cams.length) return;
    const currentIndex = cams.findIndex(d => d.deviceId === currentDeviceId);
    const next = cams[(currentIndex + 1) % cams.length];
    currentDeviceId = next.deviceId;

    const newStream = await getStream(currentDeviceId);
    const newVideoTrack = newStream.getVideoTracks()[0];
    const sender = pc && pc.getSenders().find(s => s.track && s.track.kind === 'video');
    if (sender) await sender.replaceTrack(newVideoTrack);

    // swap local preview
    if (localStream) localStream.getTracks().forEach(t => t.stop());
    localStream = newStream;
    localVideo.srcObject = localStream;
  });

  btnMinimize && btnMinimize.addEventListener('click', () => {
    callRoot.classList.toggle('h-12');
  });

  // Expose a tiny helper to initiate from anywhere
  window.One2OneCalls = {
    async call(receiverId, metadata = {}) {
      const res = await axios.post('/one2one/calls', { receiver_id: receiverId, metadata });
      currentCallUuid = res.data.call.uuid;
      showCallUI();
      await startLocalIfNeeded();
      await ensurePc();
    }
  };
})();
