<script>
  import { onMount } from 'svelte';
  let data = [];
  let country = 'usa'; // 원하는 국가로 변경 가능
  let showText = false;

  function handleClick() {
    showText = true;
  }

  onMount(async () => {
    const res = await fetch(`http://localhost/WorldPopulation/phpPart/makingDB.php?country=${country}`);
    data = await res.json();
  });
</script>

<h1>Home Page</h1>
<p>Welcome to the Home page!</p>

<button on:click={handleClick}>버튼</button>
{#if showText}
  <h1>곽우석</h1>
{/if}

{#each data as item}
  <p>{item.year}: {item.population}</p>
{/each}


