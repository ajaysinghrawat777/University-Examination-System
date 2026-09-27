import AppLayout from '@/layouts/AppLayout';

export default function Dashboard({ metrics }: { metrics: { students: number; examinations: number; published: number; pendingImports: number } }) {
  const cards = [
    ['Students', metrics.students],
    ['Examinations', metrics.examinations],
    ['Published', metrics.published],
    ['Pending Imports', metrics.pendingImports],
  ];
  return <AppLayout>
    <h1 className="mb-6 text-2xl font-bold">Dashboard</h1>
    <div className="grid gap-4 md:grid-cols-4">
      {
        cards.map(([label, value]) => <div key={String(label)} className="rounded-xl border bg-white p-5 shadow-sm"><div className="text-sm text-slate-500">{label}</div><div className="mt-2 text-3xl font-semibold">{value}</div></div>)
      }
    </div>
  </AppLayout>;
}
