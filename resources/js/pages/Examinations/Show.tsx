import AppLayout from '@/layouts/AppLayout';
import { useForm, Link } from '@inertiajs/react';

type Exam = { id: number; code: string; name: string; status: string; enrolled_count: number; course_count: number };
export default function Show({ examination }: { examination: Exam }) {
  const form = useForm<{ file: File | null }>({ file: null });
  const submit = (e: React.FormEvent) => { e.preventDefault(); form.post(`/examinations/${examination.id}/marks/imports`, { forceFormData: true }); };
  return <AppLayout><div className="mb-6"><Link href="/examinations" className="text-sm underline">← Examinations</Link><h1 className="mt-2 text-2xl font-bold">{examination.code} · {examination.name}</h1><p className="text-slate-500">Status: {examination.status} · {examination.enrolled_count} students · {examination.course_count} courses</p></div><div className="rounded-xl border bg-white p-6 shadow-sm"><h2 className="mb-3 text-lg font-semibold">Import marks</h2><p className="mb-4 text-sm text-slate-500">CSV: student_admission_no, course_code, assessment_component_code, marks</p><form onSubmit={submit} className="flex items-end gap-4"><input type="file" accept=".csv,text/csv" onChange={e=>form.setData('file', e.target.files?.[0] ?? null)} /><button disabled={form.processing} className="rounded-lg bg-slate-900 px-4 py-2 text-white disabled:opacity-50">{form.processing ? 'Uploading…' : 'Upload CSV'}</button></form>{Object.values(form.errors).map(err=><div className="mt-3 text-sm text-red-600" key={err}>{err}</div>)}</div></AppLayout>;
}
