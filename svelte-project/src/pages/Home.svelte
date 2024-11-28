<script>
  import { onMount } from 'svelte';
  let data = [];
  let country = ''; // 기본값 설정
  let countries = []; // 국가명 목록
  let showCountries = false;

  // API를 호출하여 데이터를 가져오는 함수
  async function fetchData() {
    // API 엔드포인트에 요청
    const res = await fetch(`http://localhost/WorldPopulation/phpPart/makingDB.php?country=${country}`); //이것과 같이 입력하면 해당 국가에 대한 데이터를 json으로 반환함
    data = await res.json(); // API에서 받아온 데이터

    // 예시 반환값:
    // data = [
    //   { year: 2020, population: 331002651 },
    //   { year: 2019, population: 328239523 },
    //   // ...연도에 따른 인구 데이터...
    // ];
  }

  // 국가 목록을 가져오는 함수
  async function fetchCountries() {
    const res = await fetch(`http://localhost/WorldPopulation/phpPart/makingDB.php?action=get_countries`);
    countries = await res.json();
    showCountries = true;
  }

  $: if (country) {
    fetchData();
  }
</script>

<h1>Home Page</h1>
<p>Welcome to the Home page!</p>

<input type="text" bind:value={country} placeholder="국가를 입력하세요" />

<button on:click={fetchCountries}>국가 목록 보기</button>

{#if showCountries}
  <h2>사용 가능한 국가 목록:</h2>
  <ul>
    {#each countries as c}
      <li>{c}</li>
    {/each}
  </ul>
{/if}

{#each data as item}
  <p>{item.year}: {item.population}</p>
{/each}


