let menu = document.querySelector('#menu-bars');
let navbar = document.querySelector('.navbar');

menu.onclick = () =>{
	menu.classList.toggle('fa-times');
	navbar.classList.toggle('active');
}

let themeToggler = document.querySelector('.theme-toggler');
let toggleBtn = document.querySelector('.toggle-btn');

/*toggleBtn.onclick = () =>{
	themeToggler.classList.toggle('active');
}*/
	

window.onscroll = () =>{
	menu.classList.remove('fa-times');
	navbar.classList.remove('active');
	//themeToggler.classList.remove('active');
}

/*document.querySelectorAll('.theme-slider .theme-btn').forEach(btn => {
	btn.onclick = () =>{
		let color btn.style.background;
		document.querySelector(':root').style.setProperty('--main-color',color);
	}
});*/

var sections = {
	home : document.getElementById("homeSection"),
	service : document.getElementById("serviceSection"),
	aboutus : document.getElementById("aboutusSection"),
	gallery : document.getElementById("gallerySection"),
	contact : document.getElementById("contactSection"),
	review : document.getElementById("reviewSection"),
	package : document.getElementById("packageSection"),
	bdaypack: document.getElementById("bdaySection"),
	mrgpack: document.getElementById("mrgSection"),
	cncrnpack: document.getElementById("concertSection"),
	venue : document.getElementById("venuesec")
}

function hideAllSections(){
	for(var key in sections){
		sections[key].style.display = "none";
	}
}

document.getElementById("homeLink").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.home.style.display = "block";
	window.scrollTo(0, sections.home);
});

document.getElementById("homelink").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.home.style.display = "block";
	window.scrollTo(0, sections.gallery);
});

document.getElementById("serviceLink").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.service.style.display = "grid";
	window.scrollTo(0, sections.service);
});

document.getElementById("servicelink").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.service.style.display = "grid";
	window.scrollTo(0, sections.service);
});

document.getElementById("aboutusLink").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.aboutus.style.display = "block";
	window.scrollTo(0, sections.gallery);
});

document.getElementById("venueser").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.venue.style.display = "block";
	window.scrollTo(0, sections.venue);
});

document.getElementById("aboutlink").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.aboutus.style.display = "block";
	window.scrollTo(0, sections.gallery);
});

document.getElementById("galleryLink").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.gallery.style.display = "block";
	window.scrollTo(0, sections.gallery);
});

document.getElementById("gallerylink").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.gallery.style.display = "block";
	window.scrollTo(0, sections.gallery);
});

document.getElementById("galleryser").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.gallery.style.display = "block";
	window.scrollTo(0, sections.gallery);
});

document.getElementById("contactLink").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.contact.style.display = "block";
	window.scrollTo(0, sections.gallery);
});

document.getElementById("contactlink").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.contact.style.display = "block";
	window.scrollTo(0, sections.gallery);
});

document.getElementById("contactus").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.contact.style.display = "block";
	window.scrollTo(0, sections.gallery);
});

document.getElementById("pricelink").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.package.style.display = "block";
	sections.bdaypack.style.display = "grid";
	sections.mrgpack.style.display = "grid";
	sections.cncrnpack.style.display = "grid";
	window.scrollTo(0, sections.gallery);
});

document.getElementById("packagelink").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.package.style.display = "block";
	sections.bdaypack.style.display = "grid";
	window.scrollTo(0, sections.gallery);
});

document.getElementById("packagelink1").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.package.style.display = "block";
	sections.mrgpack.style.display = "grid";
	window.scrollTo(0, sections.gallery);
});

document.getElementById("packagelink2").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.package.style.display = "block";
	sections.cncrnpack.style.display = "grid";
	window.scrollTo(0, sections.gallery);
});

document.getElementById("reviewLink").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.review.style.display = "block";
	window.scrollTo(0, sections.gallery);
});

document.getElementById("reviewlink").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.review.style.display = "block";
	window.scrollTo(0, sections.gallery);
});

document.getElementById("bdayLink").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.package.style.display = "block";
	sections.bdaypack.style.display = "grid";
	window.scrollTo(0, sections.package);
});
document.getElementById("mrgLink").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.package.style.display = "block";
	sections.mrgpack.style.display = "grid";
	window.scrollTo(0, sections.package);
});
document.getElementById("cncrnLink").addEventListener("click", function(event){
	event.preventDefault();
	hideAllSections();
	sections.package.style.display = "block";
	sections.cncrnpack.style.display = "grid";
	window.scrollTo(0, sections.package);
});




/*let currentSectionId = 'homeLink';

const navLinks = document.querySelectorAll('.nav-link');
navLinks.forEach(link =>{
	link.addEventListener('click', ()=>{
		const targetId = link.getAttribute('href').substring(1);
		const targetSection = document.getElementById(targetId);

		//hide the  current section
		const currentSection = document.getElementById(currentSectionId);
		currentSection.style.display = 'none';

		//show the target section
		targetSection.style.display = "block";

		window.scrollTo(0, targetSection.offsetTop);

		currentSectionId = targetId;
	});
});*/




var swiper = new Swiper(".home-slider", {
    effect: "coverflow",
    grabCursor: true,
    centeredSlides: true,
    slidesPerView: "auto",
    coverflowEffect: {
       rotate: 50,
       stretch: 0,
       depth: 100,
       modifier: 1,
       slideShadows: true,
    },
	loop:true,
	autoplay:{
	   delay: 3000,
	   disableOnInteraction:false,
	},
});

var rswiper = new Swiper(".review-slider", {
    slidesPerView: "auto",
	grabCursor: true,
	loop:true,
	spaceBetween: 10,
	breakpoints: {
		0: {
			slidesPerView: 1,
		},
		700: {
			slidesPerView: 2,
		},
		1050: {
			slidesPerView: 3,
		},
	},
	autoplay:{
	   delay: 300,
	   disableOnInteraction:false,
	}	
});

// initialize swiper js

const r_swiper = new Swiper('.rev-swiper', {
    loop: true,

     // If we need pagination
  pagination: {
    el: '.swiper-pagination',
  },

    // Navigation arrows
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },


})

function validateform() {
	let x= document.forms["form"]["name"].value;
	if(x==""){
		alert("field is empty");
		return false;
	}
}