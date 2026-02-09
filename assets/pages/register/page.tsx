import React, {useState} from "react";
import styled from "styled-components";

const Wrapper = styled.div`
  max-width: 600px;
  margin: 40px auto;
`;

export const RegisterPage = () => {
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [message, setMessage] = useState<string | null>(null);

    const submit = async (e: React.FormEvent) => {
        e.preventDefault();
        setMessage(null);
        try {
            const res = await fetch('/api/auth/register', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({email, password})
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'error');
            setMessage('Registered. Check var/emails for confirmation token.');
        } catch (err: any) {
            setMessage(err.message);
        }
    }

    return <Wrapper>
        <h2>Register</h2>
        <form onSubmit={submit}>
            <div>
                <label>Email</label><br/>
                <input value={email} onChange={e => setEmail(e.target.value)} />
            </div>
            <div>
                <label>Password</label><br/>
                <input type="password" value={password} onChange={e => setPassword(e.target.value)} />
            </div>
            <div style={{marginTop:10}}>
                <button type="submit">Register</button>
            </div>
        </form>
        {message && <p>{message}</p>}
    </Wrapper>
}

export default RegisterPage;
