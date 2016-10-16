   <script type="text/javascript">
   $(document ).ready(function() {
    {%  if etatChargement == false %}
       document.getElementById("loading-indicator").style.display = "none";
         {%  else %}
      document.getElementById("loading-indicator").style.display = "block";
    {%  endif  %}
   });
   </script>