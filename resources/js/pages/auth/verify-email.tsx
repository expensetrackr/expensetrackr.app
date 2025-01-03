import { Head, Link, useForm } from "@inertiajs/react";
import { useEffect } from "react";
import { toast } from "sonner";
import { route } from "ziggy-js";

import * as Button from "#/components/ui/button.tsx";
import { AuthLayout } from "#/layouts/auth-layout.tsx";

export default function VerifyEmail({ status }: { status?: string }) {
    const { post, processing } = useForm({});

    useEffect(() => {
        if (status) {
            toast.info(status);
        }
    }, [status]);

    const submit: React.FormEventHandler = (e) => {
        e.preventDefault();

        post(route("verification.send"));
    };

    return (
        <AuthLayout description="Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.">
            <Head title="Email Verification" />

            <form className="flex flex-col gap-3" onSubmit={submit}>
                <Button.Root disabled={processing} type="submit">
                    Resend verification email
                </Button.Root>

                <Button.Root $style="stroke" $type="neutral" asChild>
                    <Link as="button" href={route("logout")} method="post">
                        Log Out
                    </Link>
                </Button.Root>
            </form>
        </AuthLayout>
    );
}
