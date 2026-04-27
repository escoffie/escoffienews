import React, { useState, useEffect } from 'react';
import { Send, AlertCircle, CheckCircle2, Loader2 } from 'lucide-react';
import api from '../lib/api';

const CATEGORY_COLORS = [
    'text-orange-400 bg-orange-400/10',
    'text-emerald-400 bg-emerald-400/10',
    'text-purple-400 bg-purple-400/10',
    'text-blue-400 bg-blue-400/10',
    'text-pink-400 bg-pink-400/10'
];

export const NotificationForm = () => {
    const [categories, setCategories] = useState([]);
    const [category, setCategory] = useState('');
    const [message, setMessage] = useState('');
    const [chaosMonkey, setChaosMonkey] = useState(false);
    const [status, setStatus] = useState('idle'); // idle, loading, success, error
    const [errorMsg, setErrorMsg] = useState('');

    useEffect(() => {
        api.get('/categories')
           .then(res => setCategories(res.data))
           .catch(err => console.error('Failed to load categories', err));
    }, []);

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!category || !message) return;

        setStatus('loading');
        try {
            await api.post('/notifications', { category, message, chaos_monkey: chaosMonkey });
            setStatus('success');
            setMessage('');
            setTimeout(() => setStatus('idle'), 3000);
        } catch (err) {
            setStatus('error');
            setErrorMsg(err.response?.data?.message || 'Failed to send notification');
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-6">
            <div>
                <label className="block text-sm font-medium text-slate-400 mb-3 uppercase tracking-widest">
                    Select Category
                </label>
                <div className="grid grid-cols-3 gap-3">
                    {categories.map((cat, idx) => (
                        <button
                            key={cat.id || cat.name}
                            type="button"
                            onClick={() => setCategory(cat.name)}
                            className={`px-4 py-3 rounded-xl border-2 transition-all flex flex-col items-center gap-2 ${
                                category === cat.name 
                                ? 'border-brand-primary bg-brand-primary/10 shadow-[0_0_20px_rgba(59,130,246,0.3)]' 
                                : 'border-slate-700 bg-slate-800/50 hover:border-slate-600'
                            }`}
                        >
                            <span className={`text-xs font-bold uppercase ${CATEGORY_COLORS[idx % CATEGORY_COLORS.length]} px-2 py-0.5 rounded`}>
                                {cat.name}
                            </span>
                        </button>
                    ))}
                </div>
            </div>

            <div>
                <label className="block text-sm font-medium text-slate-400 mb-3 uppercase tracking-widest">
                    Message Body
                </label>
                <textarea
                    value={message}
                    onChange={(e) => setMessage(e.target.value)}
                    placeholder="Enter the notification message here..."
                    className="w-full h-32 bg-slate-900/50 border-2 border-slate-700 rounded-xl px-4 py-3 text-slate-200 placeholder:text-slate-600 focus:outline-none focus:border-brand-primary transition-colors resize-none"
                    required
                />
            </div>

            <div className="flex items-center justify-between p-4 rounded-xl bg-slate-900/50 border border-slate-700/50">
                <div>
                    <p className="text-sm font-bold text-slate-300">Chaos Monkey Mode</p>
                    <p className="text-xs text-slate-500">Simulate random provider failures (30% chance).</p>
                </div>
                <button
                    type="button"
                    onClick={() => setChaosMonkey(!chaosMonkey)}
                    className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${
                        chaosMonkey ? 'bg-red-500' : 'bg-slate-700'
                    }`}
                >
                    <span className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${
                        chaosMonkey ? 'translate-x-6' : 'translate-x-1'
                    }`} />
                </button>
            </div>

            <button
                type="submit"
                disabled={status === 'loading' || !category || !message}
                className="w-full bg-gradient-to-r from-brand-primary to-brand-secondary hover:shadow-[0_0_30px_rgba(99,102,241,0.5)] disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-4 rounded-xl transition-all flex items-center justify-center gap-2 group"
            >
                {status === 'loading' ? (
                    <Loader2 className="animate-spin" size={20} />
                ) : (
                    <>
                        <Send size={20} className="group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" />
                        Send Notification
                    </>
                )}
            </button>

            {status === 'success' && (
                <div className="flex items-center gap-2 text-emerald-400 bg-emerald-400/10 p-4 rounded-xl border border-emerald-400/20 animate-in fade-in slide-in-from-top-2">
                    <CheckCircle2 size={20} />
                    <span className="text-sm font-medium">Notification dispatched successfully!</span>
                </div>
            )}

            {status === 'error' && (
                <div className="flex items-center gap-2 text-red-400 bg-red-400/10 p-4 rounded-xl border border-red-400/20">
                    <AlertCircle size={20} />
                    <span className="text-sm font-medium">{errorMsg}</span>
                </div>
            )}
        </form>
    );
};
