import { Head } from "@inertiajs/react";
import Share08SolidIcon from "virtual:icons/hugeicons/share-08-solid";

import ConnectedAccountsForm from "#/components/forms/connected-accounts-form.tsx";
import { Header } from "#/components/page-header.tsx";
import * as Divider from "#/components/ui/divider.tsx";
import { SettingsLayout } from "#/layouts/settings-layout.tsx";

export default function ConnectedAccountsShow() {
    return (
        <>
            <div className="px-4 lg:px-8">
                <Divider.Root />
            </div>

            <div className="flex w-full flex-col gap-5 px-4 py-6 lg:px-8">
                <ConnectedAccountsForm />
            </div>
        </>
    );
}

ConnectedAccountsShow.layout = (page: React.ReactNode & { props: App.Data.Shared.SharedInertiaData }) => (
    <SettingsLayout {...page.props}>
        <Head title="Social accounts" />

        <Header
            description="Manage your social connected accounts."
            icon={
                <div className="flex size-12 shrink-0 items-center justify-center rounded-full bg-(--bg-white-0) shadow-xs ring-1 ring-(--stroke-soft-200) ring-inset">
                    <Share08SolidIcon className="size-6 text-(--text-sub-600)" />
                </div>
            }
            title="Connected accounts"
        />

        {page}
    </SettingsLayout>
);
