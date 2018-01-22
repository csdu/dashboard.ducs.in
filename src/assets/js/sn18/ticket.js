const $id = id => document.getElementById(id);

const $qrcode = $id('qrcode');
const $sizeQR = Math.min(window.innerWidth * 0.8, 300);
new QRCode($qrcode, {
  text: $qrcode.dataset.text,
  width: $sizeQR,
  height: $sizeQR,
  correctLevel: QRCode.CorrectLevel.H,
});
$qrcode.removeAttribute('title');

const $secret = $id('secret');
const toggleSecret = () => {
  $secret.innerText = $secret.classList.contains('active') ?
    $secret.dataset.mask :
    $secret.dataset.text;
  $secret.classList.toggle('active');
};
$secret.addEventListener('click', toggleSecret);
