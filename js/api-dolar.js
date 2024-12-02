 // La URL de la API de ExchangeRate-API con tu clave
 const apiKey = 'f4a768e68a20b957a791dff8';
 const apiUrl = `https://v6.exchangerate-api.com/v6/${apiKey}/latest/USD`;

 // Función para obtener el precio del dólar
 async function fetchDollarPrice() {
     try {
         const response = await fetch(apiUrl);
         if (!response.ok) {
             throw new Error('Network response was not ok');
         }
         const data = await response.json();
         // Supongamos que el precio del dólar está en data.conversion_rates.VES
         const priceInVES = data.conversion_rates.VES;
         return priceInVES;
     } catch (error) {
         console.error('Error fetching dollar price:', error);
         return null;
     }
 }

 // Función para actualizar el header con el precio del dólar
 async function updateHeaderWithDollarPrice() {
     const price = await fetchDollarPrice();
     if (price !== null) {
         const headerElement = document.getElementById('dollar-price');
         if (headerElement) {
             // Limitar el precio a dos decimales
             headerElement.textContent = `${price.toFixed(2)} VES`;
         }
     }
 }

 // Llama a la función para actualizar el header cuando la página cargue
 document.addEventListener('DOMContentLoaded', updateHeaderWithDollarPrice); 