AOS.init({ once: true, duration: 1500, easing: "ease-in-out" });

const mobileMenuBtn = document.getElementById("mobile-menu-btn");
const mobileMenu = document.getElementById("mobile-menu");

if (mobileMenuBtn) {
  mobileMenuBtn.addEventListener("click", () => {
    mobileMenu.classList.toggle("hidden");
  });
}

function toggleSubMenu(id) {
  const submenu = document.getElementById(id);
  if (submenu) {
    submenu.classList.toggle("hidden");
  }
}

document.querySelectorAll(".group").forEach(group => {
  group.addEventListener("mouseenter", () => {
    const ul = group.querySelector("ul");
    if (ul) ul.classList.remove("hidden");
  });
  group.addEventListener("mouseleave", () => {
    const ul = group.querySelector("ul");
    if (ul) ul.classList.add("hidden");
  });
});

const animateElements = document.querySelectorAll("[data-animate]");

const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const el = entry.target;
        const direction = el.dataset.direction || "up";
        const delay = parseInt(el.dataset.delay) || 0;

        let initialClass = "";
        let finalClass = "opacity-100 translate-x-0 translate-y-0";

        switch (direction) {
          case "up":
            initialClass = "opacity-0 translate-y-16";
            break;
          case "down":
            initialClass = "opacity-0 -translate-y-16";
            break;
          case "left":
            initialClass = "opacity-0 translate-x-16";
            break;
          case "right":
            initialClass = "opacity-0 -translate-x-16";
            break;
        }

        el.classList.add(...initialClass.split(" "));

        setTimeout(() => {
          el.classList.remove(...initialClass.split(" "));
          el.classList.add(...finalClass.split(" "));
          el.style.transition = "all 0.8s cubic-bezier(0.22, 1, 0.36, 1)";
        }, delay);

        observer.unobserve(el);
      }
    });
  },
  { threshold: 0.2 }
);

animateElements.forEach((el) => observer.observe(el));


const carousel = document.getElementById("carousel");
if (carousel) {
  const originalCards = Array.from(
    document.querySelectorAll(".testimonial-card")
  );
  let cards = [...originalCards];

  if (originalCards.length > 0) {
    const firstClone = originalCards[0].cloneNode(true);
    const lastClone = originalCards[originalCards.length - 1].cloneNode(true);
    carousel.appendChild(firstClone);
    carousel.insertBefore(lastClone, carousel.firstChild);
  }

  cards = Array.from(document.querySelectorAll(".testimonial-card"));
  let currentIndex = 1;

  function updateCarousel(animate = true) {
    if (cards.length === 0) return;
    cards.forEach((card, index) => {
      card.classList.remove("scale-105", "opacity-100", "blur-none");
      card.classList.add("scale-95", "opacity-60", "blur-sm");
      if (index === currentIndex) {
        card.classList.remove("scale-95", "opacity-60", "blur-sm");
        card.classList.add("scale-105", "opacity-100", "blur-none");
      }
    });

    const carouselWidth = carousel.offsetWidth;
    const cardWidth = cards[0].offsetWidth + 16;
    let offset = cardWidth * currentIndex - carouselWidth / 2 + cardWidth / 2;

    if (!animate) carousel.style.transition = "none";
    else carousel.style.transition = "transform 0.5s";

    carousel.style.transform = `translateX(-${offset}px)`;
  }

  function moveNext() {
    currentIndex++;
    updateCarousel();
    if (currentIndex === cards.length - 1) {
      setTimeout(() => {
        currentIndex = 1;
        updateCarousel(false);
      }, 500);
    }
  }

  function movePrev() {
    currentIndex--;
    updateCarousel();
    if (currentIndex === 0) {
      setTimeout(() => {
        currentIndex = cards.length - 2;
        updateCarousel(false);
      }, 500);
    }
  }

  const nextBtn = document.getElementById("nextBtn");
  const prevBtn = document.getElementById("prevBtn");

  if (nextBtn) nextBtn.addEventListener("click", moveNext);
  if (prevBtn) prevBtn.addEventListener("click", movePrev);

  let startX = 0;
  let isDragging = false;

  carousel.addEventListener("pointerdown", (e) => {
    startX = e.clientX;
    isDragging = true;
    carousel.style.transition = "none";
  });

  carousel.addEventListener("pointermove", (e) => {
    if (!isDragging) return;
    const moveX = e.clientX - startX;
    carousel.style.transform = `translateX(calc(-${cards[0].offsetWidth * currentIndex -
      carousel.offsetWidth / 2 +
      cards[0].offsetWidth / 2
      }px + ${-moveX}px))`;
  });

  carousel.addEventListener("pointerup", (e) => {
    isDragging = false;
    const endX = e.clientX;
    const diff = endX - startX;
    if (diff < -50) moveNext();
    else if (diff > 50) movePrev();
    else updateCarousel();
  });

  carousel.addEventListener("pointerleave", (e) => {
    if (isDragging) {
      isDragging = false;
      updateCarousel();
    }
  });

  updateCarousel(false);
  window.addEventListener("resize", () => updateCarousel(false));
}

document.addEventListener("DOMContentLoaded", function () {
  let device_width = window.innerWidth;

  if (document.querySelector(".progress-wrap")) {
    var progressPath = document.querySelector(".progress-wrap path");
    var pathLength = progressPath.getTotalLength();
    progressPath.style.transition = progressPath.style.WebkitTransition = "none";
    progressPath.style.strokeDasharray = pathLength + " " + pathLength;
    progressPath.style.strokeDashoffset = pathLength;
    progressPath.getBoundingClientRect();
    progressPath.style.transition = progressPath.style.WebkitTransition = "stroke-dashoffset 10ms linear";

    var updateProgress = function () {
      var scroll = window.scrollY;
      var height = document.documentElement.scrollHeight - window.innerHeight;
      var progress = pathLength - (scroll * pathLength) / height;
      progressPath.style.strokeDashoffset = progress;
    };

    updateProgress();
    window.addEventListener("scroll", updateProgress);

    var offset = 50;
    window.addEventListener("scroll", function () {
      if (window.scrollY > offset) {
        document.querySelector(".progress-wrap").classList.add("active-progress");
        document.querySelector(".whatsapp-wrap").classList.add("active-progress");
      } else {
        document.querySelector(".progress-wrap").classList.remove("active-progress");
        document.querySelector(".whatsapp-wrap").classList.remove("active-progress");
      }
    });

    document.querySelector(".progress-wrap").addEventListener("click", function (event) {
      event.preventDefault();
      window.scrollTo({ top: 0, behavior: "smooth" });
      return false;
    });

    var initialScroll = window.scrollY;
    if (initialScroll >= 50) {
      document.querySelector(".progress-wrap").classList.add("active-progress");
      document.querySelector(".whatsapp-wrap").classList.add("active-progress");
    }
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const header = document.getElementById("main-header");
  if (!header) return;
  const mode = "bg-and-text";
  const SCROLL_OFFSET = 50;
  function onScroll() {
    if (window.scrollY > SCROLL_OFFSET) {
      if (mode === "bg-and-text") {
        header.classList.add("scrolled");
        header.classList.remove("scrolled-text-only");
      } else {
        header.classList.add("scrolled-text-only");
        header.classList.remove("scrolled");
      }
    } else {
      header.classList.remove("scrolled", "scrolled-text-only");
    }
  }
  onScroll();
  window.addEventListener("scroll", onScroll);
});