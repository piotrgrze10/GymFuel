document.addEventListener('DOMContentLoaded', function () {
	const nav = document.querySelector('.navbar')
	const allNavItems = document.querySelectorAll('.nav-link')
	const navList = document.querySelector('.navbar-collapse')
	const navbarToggler = document.querySelector('.navbar-toggler')

	function addShadow() {
		nav.classList.add('shadow-bg')
	}

	navbarToggler.addEventListener('click', () => {
		addShadow()
	})

	allNavItems.forEach(item =>
		item.addEventListener('click', () => {
			navList.classList.remove('show')
		})
	)

	window.addEventListener('scroll', addShadow)
	window.addEventListener('click', addShadow)
})
let products = []
let currentPage = 0
const resultsPerPage = 8
let filteredProducts = []

fetch('food1.json')
	.then(response => response.json())
	.then(data => {
		products = data
	})
	.catch(error => {
		console.error('Błąd podczas ładowania danych:', error)
	})

function initializeSearch() {
	document.getElementById('searchInput').addEventListener('keyup', searchProducts)
}

function searchProducts() {
	const input = document.getElementById('searchInput')
	const filter = input.value.toLowerCase()
	currentPage = 0

	if (filter === '') {
		filteredProducts = []
	} else {
		filteredProducts = products.filter(
			product =>
				product.description.toLowerCase().includes(filter) ||
				product.foodNutrients.some(nutrient => nutrient.nutrient.name.toLowerCase().includes(filter))
		)
	}

	displayResults()
}

function displayResults() {
	const resultsDiv = document.getElementById('results')
	resultsDiv.innerHTML = ''

	filteredProducts.forEach(product => {
		const productDiv = document.createElement('div')
		productDiv.className = 'product'

		const nameDiv = document.createElement('div')
		nameDiv.textContent = product.description
		nameDiv.className = 'name'
		productDiv.appendChild(nameDiv)

		const enterButton = document.createElement('button')
		enterButton.textContent = 'Enter Amount'
		enterButton.onclick = function () {
			window.location.href = `enter_amount.php?productName=${encodeURIComponent(product.description)}`
		}
		productDiv.appendChild(enterButton)

		resultsDiv.appendChild(productDiv)
	})
}

document.addEventListener('DOMContentLoaded', function () {
	let currentDate = new Date('<?php echo $date; ?>')

	function updateDisplayDate() {
		const options = { year: 'numeric', month: 'long', day: 'numeric' }
		document.getElementById('date').textContent = currentDate.toLocaleDateString('en-US', options)
	}

	function changeDate(days) {
		currentDate.setDate(currentDate.getDate() + days)
		let dateString = currentDate.toISOString().split('T')[0]
		window.location.href = `?date=${dateString}`
	}

	document.getElementById('prev-day').addEventListener('click', function () {
		changeDate(-1)
	})
	document.getElementById('next-day').addEventListener('click', function () {
		changeDate(1)
	})

	updateDisplayDate()
})
