  <style>
      /* Spinner simples */
      .spinner {
          width: 40px;
          height: 40px;
          border: 4px solid #ccc;
          border-top: 4px solid #3498db;
          border-radius: 50%;
          animation: girar 1s linear infinite;
          margin: 50px auto;
      }

      @keyframes girar {
          0% {
              transform: rotate(0deg);
          }

          100% {
              transform: rotate(360deg);
          }
      }

      /* Texto abaixo do spinner */
      .texto {
          text-align: center;
          font-family: Arial, sans-serif;
          color: #333;
          margin-top: 10px;
      }
  </style>
  <div id="spinner-container">
      <div class="spinner"></div>
      <div class="texto">Processando...</div>
  </div>

  <script>
     
      setTimeout(() => {
          const spinner = document.getElementById('spinner-container');
          if (spinner) spinner.style.display = 'none';
      }, 500); 
  </script>
