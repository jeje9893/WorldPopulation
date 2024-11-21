<script>
  import { Router, Route, Link } from "svelte-routing";

  // 페이지 컴포넌트 임포트
  import Home from './pages/Home.svelte';
  import About from './pages/About.svelte';
  import Contact from './pages/Contact.svelte';
</script>

<Router>
  <!-- 내비게이션 링크 -->
  <nav>
    <Link to="/">Home</Link>
    <Link to="/about">About</Link>
    <Link to="/contact">Contact</Link>
  </nav>

  <!-- 라우팅 설정 -->
  <Route path="/" component={Home} />
  <Route path="/about" component={About} />
  <Route path="/contact" component={Contact} />
</Router>
