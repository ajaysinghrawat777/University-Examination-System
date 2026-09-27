import AppLayout from '@/layouts/AppLayout';

type ImportData = { id: string; status: string; total_rows: number; processed_rows: number; failed_rows: number; completed_at: string | null };
export default function Show({ importData }: { importData: ImportData }) {
  return <AppLayout><h1 className="text-2xl font-bold">Import {importData.id}</h1><div className="mt-6 grid gap-4 md:grid-cols-4">{[['Status', importData.status], ['Rows', importData.total_rows], ['Processed', importData.processed_rows], ['Errors', importData.failed_rows]].map(([l, v]) => <div key={String(l)} className="rounded-xl border bg-white p-5"><div className="text-sm text-slate-500">{l}</div><div className="mt-2 text-xl font-semibold">{v}</div></div>)}</div></AppLayout>;
}
