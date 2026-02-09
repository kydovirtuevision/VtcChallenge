import React, {useEffect, useState} from "react";
import styled from "styled-components";

const Wrapper = styled.div`
  max-width: 900px;
  margin: 20px auto;
`;

type Note = {id:number, title:string, content:string, category?:string, status:string};

export const NotesPage = ({token}:{token?:string}) => {
    const [notes, setNotes] = useState<Note[]>([]);
    const [title, setTitle] = useState('');
    const [content, setContent] = useState('');
    const [category, setCategory] = useState('');
    const [status, setStatus] = useState('new');

    const load = async () => {
        const t = token || localStorage.getItem('api_token');
        if (!t) return;
        const res = await fetch('/api/notes/?q=&status=&category=', {
            headers: { 'Authorization': 'Bearer ' + t }
        });
        if (!res.ok) return;
        const data = await res.json();
        setNotes(data);
    }

    useEffect(()=>{ load(); }, []);

    const submit = async (e: React.FormEvent) => {
        e.preventDefault();
        const t = token || localStorage.getItem('api_token');
        if (!t) return alert('No token');
        const res = await fetch('/api/notes/', {
            method: 'POST',
            headers: {'Content-Type':'application/json', 'Authorization': 'Bearer ' + t},
            body: JSON.stringify({title, content, category, status})
        });
        if (res.status === 201) {
            setTitle(''); setContent(''); setCategory(''); setStatus('new');
            load();
        } else {
            const d = await res.json();
            alert(d.error || 'error');
        }
    }

    return <Wrapper>
        <h2>Your Notes</h2>
        <form onSubmit={submit} style={{marginBottom:20}}>
            <div><input placeholder="Title" value={title} onChange={e=>setTitle(e.target.value)} /></div>
            <div><textarea placeholder="Content" value={content} onChange={e=>setContent(e.target.value)} /></div>
            <div>
                <input placeholder="Category" value={category} onChange={e=>setCategory(e.target.value)} />
                <select value={status} onChange={e=>setStatus(e.target.value)}>
                    <option value="new">new</option>
                    <option value="todo">todo</option>
                    <option value="done">done</option>
                </select>
                <button type="submit">Add</button>
            </div>
        </form>

        <div>
            {notes.map(n => (
                <div key={n.id} style={{border:'1px solid #ddd', padding:8, marginBottom:8}}>
                    <h4>{n.title} <small>({n.status})</small></h4>
                    <p>{n.content}</p>
                    <small>{n.category}</small>
                </div>
            ))}
        </div>
    </Wrapper>
}

export default NotesPage;
