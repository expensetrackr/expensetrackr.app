import { Head } from "@inertiajs/react";
import { formatCurrency } from "@sumup/intl";
import MoneyDollarCircleFillIcon from "virtual:icons/ri/money-dollar-circle-fill";

import { Button } from "#/components/button.tsx";
import { ContentDivider } from "#/components/content-divider.tsx";
import { Text } from "#/components/text.tsx";
import { useAccountForm } from "#/hooks/use-account-form.ts";
import { CreateLayout } from "#/layouts/create-layout.tsx";
import { type AccountFormData } from "#/models/account.ts";
import { type InertiaSharedProps } from "#/types/index.ts";

type CreateAccountReviewPageProps = {
    formData: AccountFormData;
};

export default function CreateAccountReviewPage({
    formData,
    ...props
}: InertiaSharedProps<CreateAccountReviewPageProps>) {
    const form = useAccountForm(formData);

    function handleSubmit(e: React.FormEvent<HTMLFormElement> | React.MouseEvent<HTMLButtonElement>) {
        e.preventDefault();

        form.post(route("accounts.store", ["review"]));
    }

    return (
        <CreateLayout {...props}>
            <Head title="Review - Create Account" />

            <div className="relative py-12">
                <div className="flex flex-col gap-6 sm:mx-auto sm:max-w-[540px]">
                    <div className="flex flex-col items-center gap-2">
                        <div className="rounded-full bg-gradient-to-b from-neutral-500/10 to-transparent p-4 backdrop-blur-md">
                            <div className="rounded-full border border-[var(--stroke-soft-200)] bg-[var(--bg-white-0)] p-4">
                                <MoneyDollarCircleFillIcon className="size-8 text-[var(--icon-sub-600)]" />
                            </div>
                        </div>

                        <div className="flex flex-col items-center gap-1">
                            <h1 className="text-h5">Account Summary</h1>
                            <Text className="text-paragraph-md text-center">
                                Review your account details and confirm the creation of your new account.
                            </Text>
                        </div>
                    </div>

                    <div className="rounded-20 mx-auto w-full max-w-[400px] border border-[var(--stroke-soft-200)] bg-[var(--bg-white-0)] shadow-xs">
                        <div className="flex flex-col gap-1 p-4">
                            <Text className="text-paragraph-xs text-center">Initial balance</Text>
                            <p className="text-h4 font-display text-center">
                                {formatCurrency(
                                    // @ts-expect-error - doesn't matter the type because the format will convert it
                                    formData.initial_balance,
                                    "en",
                                    formData.currency_code,
                                )}
                            </p>
                        </div>

                        <ContentDivider $type="solid-text">account details</ContentDivider>

                        <div className="flex flex-col gap-2 p-4">
                            <div className="flex items-center gap-3">
                                <Text className="flex-1">Name</Text>
                                <p className="text-label-sm">{formData.name}</p>
                            </div>
                            <div className="flex items-center gap-3">
                                <Text className="flex-1">Currency</Text>
                                <p className="text-label-sm">{formData.currency_code}</p>
                            </div>
                            <div className="flex items-center gap-3">
                                <Text className="flex-1">Type</Text>
                                <p className="text-label-sm capitalize">{formData.type}</p>
                            </div>
                        </div>

                        <ContentDivider $type="solid-text">more</ContentDivider>

                        <div className="flex flex-col gap-2 p-4">
                            <div className="flex items-center gap-3">
                                <Text className="flex-1">Description</Text>
                                <p className="text-label-sm">{formData.description ?? "——"}</p>
                            </div>
                        </div>

                        <div className="flex items-center gap-3 border-t border-t-[var(--stroke-soft-200)] px-5 py-4">
                            <Button
                                $color="neutral"
                                $size="sm"
                                $variant="stroke"
                                className="w-full"
                                href={route("accounts.create", ["balance-and-currency"])}
                            >
                                Discard
                            </Button>
                            <Button $size="sm" className="w-full" onClick={handleSubmit}>
                                Save account
                            </Button>
                        </div>

                        <div className="px-4 pb-4">
                            <Text className="text-paragraph-xs text-center text-[var(--text-soft-400)]">
                                You can change the account details later after the account is created.
                            </Text>
                        </div>
                    </div>
                </div>
            </div>
        </CreateLayout>
    );
}