/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import React from 'react';
import ReactDOM from 'react-dom';
import './styles/app.scss';

import { IndexPage } from "./pages/index/page";
import { createRoot } from "react-dom/client";
import RegisterPage from "./pages/register/page";
import LoginPage from "./pages/login/page";
import NotesPage from "./pages/notes/page";

function getRoute() {
    return (window.location.hash || '#/').replace(/^#/, '');
}

const App = () => {
    const [route, setRoute] = React.useState(getRoute());
    React.useEffect(()=>{
        const onHash = () => setRoute(getRoute());
        window.addEventListener('hashchange', onHash);
        return () => window.removeEventListener('hashchange', onHash);
    },[]);

    const token = localStorage.getItem('api_token') || undefined;

    let view = null;
    if (route === '/' || route === '') view = <IndexPage />;
    if (route === '/register') view = <RegisterPage />;
    if (route === '/login') view = <LoginPage onLogin={()=>{ window.location.hash = '#/notes' }} />;
    if (route === '/notes') view = <NotesPage token={token} />;

    return (<div>
        <nav style={{padding:10, borderBottom:'1px solid #eee'}}>
            <a href="#/">Home</a> | <a href="#/register">Register</a> | <a href="#/login">Login</a> | <a href="#/notes">Notes</a>
        </nav>
        <div id="app-root" style={{padding:20}}>
            {view}
        </div>
    </div>);
}

const rootNode = createRoot(
    document.getElementById('app')
);

rootNode.render(<App />)
