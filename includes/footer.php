<footer class="text-center py-3 mt-auto">
  Powered by In2Grow, 2025.
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Sticky navbar
  document.addEventListener("DOMContentLoaded", function() {
    var navbar = document.querySelector('.navbar');
    function onScroll() {
      if (window.scrollY > 30) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    }
    window.addEventListener('scroll', onScroll);
    onScroll();
  });
</script>
<script>
  function handlePasswordChange(btnId) {
    const btn = document.getElementById(btnId);
    if (!btn) return;
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const newPassword = prompt('Podaj nowe hasło do testu:');
      if (newPassword && newPassword.length > 0) {
        document.getElementById('hiddenNewPassword').value = newPassword;
        document.getElementById('changePasswordForm').submit();
      }
    });
  }
  handlePasswordChange('changePasswordBtn');
  handlePasswordChange('changePasswordBtnMobile');
</script>
<script>
  // Quiz progress (jeśli dotyczy quizu)
  document.addEventListener('DOMContentLoaded', () => {
    if (window.location.href.includes('page=quiz')) {
      const title = document.getElementById('heroTitle');
      const progressBar = document.getElementById('quizProgressBar');
      const cards = document.querySelectorAll('.question-card');
      const total = cards.length;

      if (!title || !progressBar || total === 0) return;

      const updateProgress = (index) => {
        title.textContent = `Pytanie ${index + 1}`;
        const percent = Math.round(((index + 1) / total) * 100);
        progressBar.style.width = percent + '%';
      };

      cards.forEach((card, index) => {
        const confirmBtn = card.querySelector('.confirm-btn');
        confirmBtn?.addEventListener('click', () => {
          setTimeout(() => {
            const nextCard = document.querySelector('.question-card.active');
            if (nextCard) {
              const nextIndex = parseInt(nextCard.dataset.index);
              updateProgress(nextIndex);
            }
          }, 400);
        });
      });

      updateProgress(0);
    }
  });
</script>
<script>
  // Preloader (tylko raz!)
  const preloadStart = Date.now();

  window.addEventListener('load', function () {
    const preloader = document.getElementById('preloader');
    const elapsed = Date.now() - preloadStart;
    const delay = Math.max(1000 - elapsed, 0);

    setTimeout(() => {
      preloader.style.opacity = '0';
      setTimeout(() => {
        preloader.style.display = 'none';
      }, 500);
    }, delay);
  });

  document.querySelectorAll('a[href^="index.php"]').forEach(link => {
    link.addEventListener('click', function (e) {
      const target = this.getAttribute('href');
      if (target && target !== window.location.href) {
        const preloader = document.getElementById('preloader');
        preloader.style.display = 'flex';
        preloader.style.opacity = '1';
      }
    });
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var alert = document.querySelector('.alert-dismissible');
    if(alert) {
      setTimeout(function() {
        var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
        bsAlert.close();
      }, 4000); // 4 sekundy
    }
  });
</script>

</body>
</html>